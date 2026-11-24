<#
.SYNOPSIS
    Benchmarks exiftool invocations used by `exif:extract` and checks that the optimized
    flags produce the same values for the fields imported into db_foto.photos.

.DESCRIPTION
    Variants:
      old    - flags used today by ExtractExifAction (-file:all -xmp:all -exif:all -iptc:all ...)
      new    - only the tags read by ExifData::fromArray(), -e (no composite tags), -ext jpg/jpeg
      fast   - new + -fast
      fast2  - new + -fast2
      nohash - new without ImageDataHash (optional, only to measure the cost of the hash)

    All the variants receive the sample folder through a UTF-8 argfile together with
    "-charset filename=utf8", so accented folder names are handled the same way by every variant.

    Every run writes the raw exiftool stdout/stderr bytes to <OutDir>\<variant>.json / .err.
    At the end each variant is compared with "old" and a summary is written to <OutDir>\results.txt.

.PARAMETER Sample
    Folder with a representative subset of the photos (e.g. one year, 10-30 GB).

.PARAMETER ExifTool
    Path to exiftool.exe (default: "exiftool" found in PATH). Do not use "exiftool(-k).exe".

.PARAMETER OutDir
    Folder for the outputs (default: %TEMP%\exifbench). Must NOT be inside the sample folder.

.PARAMETER Rounds
    How many times every variant is run (default 2). Without -RamMap the first round reads
    from disk, the following ones mostly from the Windows file cache.

.PARAMETER RamMap
    Optional path to Sysinternals RAMMap(64).exe. When given, the standby list (file cache)
    is emptied before every run, so every run reads from disk. Requires an elevated shell.

.PARAMETER Variants
    Variants to run (default: old,new,fast,fast2). Add "nohash" to measure the hash cost.

.PARAMETER SkipDiskFloor
    Skip the plain read of all the JPEG files (the minimum time any variant can reach).

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\exif-benchmark.ps1 -Sample "D:\Foto\2017"

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\exif-benchmark.ps1 -Sample "D:\Foto\2017" `
        -ExifTool "C:\exiftool\exiftool.exe" -RamMap "C:\Sysinternals\RAMMap64.exe" -Rounds 1
#>
[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)][string]$Sample,
    [string]$ExifTool = 'exiftool',
    [string]$OutDir = (Join-Path $env:TEMP 'exifbench'),
    [ValidateRange(1, 10)][int]$Rounds = 2,
    [string]$RamMap,
    [string[]]$Variants = @('old', 'new', 'fast', 'fast2'),
    [switch]$SkipDiskFloor
)

$ErrorActionPreference = 'Stop'

# ---------------------------------------------------------------------------------------------
# Setup
# ---------------------------------------------------------------------------------------------

$Sample = (Resolve-Path -LiteralPath $Sample).ProviderPath
if ($Sample -notmatch '^[A-Za-z]:\\$') {
    $Sample = $Sample.TrimEnd('\')
}

$exifToolCommand = Get-Command $ExifTool -ErrorAction SilentlyContinue
if (-not $exifToolCommand) {
    throw "exiftool not found: '$ExifTool'. Pass the full path with -ExifTool."
}
$ExifTool = $exifToolCommand.Source
if (-not $ExifTool) {
    $ExifTool = $exifToolCommand.Path
}

if ($RamMap -and -not (Test-Path -LiteralPath $RamMap)) {
    throw "RAMMap not found: '$RamMap'."
}

New-Item -ItemType Directory -Force -Path $OutDir | Out-Null
$OutDir = (Resolve-Path -LiteralPath $OutDir).ProviderPath
if ($OutDir.StartsWith($Sample + '\', [StringComparison]::OrdinalIgnoreCase)) {
    throw "-OutDir must not be inside the sample folder."
}

$utf8NoBom = New-Object System.Text.UTF8Encoding $false
$jsonFields = @(
    'File:ImageDataHash',
    'XMP-dc:Subject',
    'XMP-mwg-rs:RegionInfo',
    'XMP-xmp:CreateDate',
    'File:ImageWidth',
    'File:ImageHeight',
    'File:MIMEType',
    'System:FileSize',
    'System:FileName',
    'System:Directory'
)
$importedTags = @(
    '-System:FileName',
    '-System:Directory',
    '-System:FileSize',
    '-File:MIMEType',
    '-File:ImageWidth',
    '-File:ImageHeight',
    '-File:ImageDataHash',
    '-XMP-dc:Subject',
    '-XMP-mwg-rs:RegionInfo',
    '-XMP-xmp:CreateDate'
)
$newBase = @('-r', '-n', '-struct', '-G1', '-e', '-ext', 'jpg', '-ext', 'jpeg')

$variantArgs = [ordered]@{
    old    = @('-r', '-n', '-a', '-struct', '-G1', '-file:all', '-xmp:all', '-exif:all', '-iptc:all', '-ImageDataHash', '-json')
    new    = $newBase + $importedTags + @('-json')
    fast   = $newBase + @('-fast') + $importedTags + @('-json')
    fast2  = $newBase + @('-fast2') + $importedTags + @('-json')
    nohash = $newBase + ($importedTags | Where-Object { $_ -ne '-File:ImageDataHash' }) + @('-json')
}

# "-File" passes "-Variants a,b" as one string: split it, then validate
$Variants = @($Variants | ForEach-Object { $_ -split ',' } | ForEach-Object { $_.Trim() } | Where-Object { $_ -ne '' })
$unknownVariants = @($Variants | Where-Object { -not $variantArgs.Contains($_) })
if ($unknownVariants.Count -gt 0) {
    throw "Unknown variant(s): $($unknownVariants -join ', '). Allowed: $($variantArgs.Keys -join ', ')."
}

# keep the order of $variantArgs ("old" first) whatever order the user typed
$selectedVariants = @($variantArgs.Keys | Where-Object { $Variants -contains $_ })

function ConvertTo-WindowsArgument([string]$value) {
    if ($value -ne '' -and $value -notmatch '[\s"]') {
        return $value
    }
    $value = $value -replace '(\\*)"', '$1$1\"'
    $value = $value -replace '(\\+)$', '$1$1'
    return '"' + $value + '"'
}

function Clear-FileCache {
    if (-not $RamMap) {
        return
    }
    Write-Host '  emptying the standby list with RAMMap...'
    $process = Start-Process -FilePath $RamMap -ArgumentList '-accepteula', '-Et' -Wait -PassThru -NoNewWindow
    if ($process.ExitCode -ne 0) {
        Write-Warning "RAMMap exited with code $($process.ExitCode) (is the shell elevated?)"
    }
    Start-Sleep -Seconds 2
}

function Invoke-ExifTool([string]$name, [string[]]$exifArgs) {
    $argFile = Join-Path $OutDir "$name.args"
    $jsonFile = Join-Path $OutDir "$name.json"
    $errFile = Join-Path $OutDir "$name.err"

    # one argument per line, no shell quoting; the sample is passed here as well
    [IO.File]::WriteAllLines($argFile, [string[]]($exifArgs + @($Sample)), $utf8NoBom)

    $startInfo = New-Object System.Diagnostics.ProcessStartInfo
    $startInfo.FileName = $ExifTool
    # "-charset filename=utf8" must come before "-@"
    $startInfo.Arguments = '-charset filename=utf8 -@ ' + (ConvertTo-WindowsArgument $argFile)
    $startInfo.UseShellExecute = $false
    $startInfo.RedirectStandardOutput = $true
    $startInfo.RedirectStandardError = $true
    $startInfo.CreateNoWindow = $true

    $jsonStream = [IO.File]::Create($jsonFile)
    $errStream = [IO.File]::Create($errFile)
    try {
        $stopwatch = [Diagnostics.Stopwatch]::StartNew()
        $process = [Diagnostics.Process]::Start($startInfo)
        # copy raw bytes: PowerShell redirection (>) would re-encode the output as UTF-16
        $copyOut = $process.StandardOutput.BaseStream.CopyToAsync($jsonStream)
        $copyErr = $process.StandardError.BaseStream.CopyToAsync($errStream)
        $process.WaitForExit()
        $copyOut.Wait()
        $copyErr.Wait()
        $stopwatch.Stop()
        $exitCode = $process.ExitCode
    }
    finally {
        $jsonStream.Dispose()
        $errStream.Dispose()
    }

    return [pscustomobject]@{
        Seconds  = [math]::Round($stopwatch.Elapsed.TotalSeconds, 1)
        ExitCode = $exitCode
        JsonMB   = [math]::Round((Get-Item -LiteralPath $jsonFile).Length / 1MB, 2)
    }
}

$usePs6Json = $PSVersionTable.PSVersion.Major -ge 6
if (-not $usePs6Json) {
    Add-Type -AssemblyName System.Web.Extensions
    $serializer = New-Object System.Web.Script.Serialization.JavaScriptSerializer
    $serializer.MaxJsonLength = [int]::MaxValue
    $serializer.RecursionLimit = 100
}

function Read-ExifJson([string]$path) {
    $text = [IO.File]::ReadAllText($path, [Text.Encoding]::UTF8)
    $bySourceFile = @{}
    if ($text.Trim() -eq '') {
        return $bySourceFile
    }
    if ($usePs6Json) {
        $records = ConvertFrom-Json -InputObject $text -AsHashtable -Depth 100
    }
    else {
        $records = $serializer.DeserializeObject($text)
    }
    foreach ($record in $records) {
        $bySourceFile[[string]$record['SourceFile']] = $record
    }
    return $bySourceFile
}

function Get-Field($record, [string]$key) {
    if ($record.ContainsKey($key)) {
        return $record[$key]
    }
    return $null
}

function ConvertTo-Canonical($value) {
    if ($null -eq $value) {
        return '<missing>'
    }
    if ($usePs6Json) {
        return (ConvertTo-Json -InputObject $value -Compress -Depth 100)
    }
    return $serializer.Serialize($value)
}

# ---------------------------------------------------------------------------------------------
# Environment info
# ---------------------------------------------------------------------------------------------

$exifToolVersion = (& $ExifTool -ver | Out-String).Trim()
try {
    $ramGB = [math]::Round((Get-CimInstance Win32_ComputerSystem).TotalPhysicalMemory / 1GB, 1)
}
catch {
    $ramGB = 'n/a'
}

Write-Host "exiftool $exifToolVersion ($ExifTool)"
Write-Host "sample   $Sample"
Write-Host "output   $OutDir"

$jpegFiles = @(Get-ChildItem -LiteralPath $Sample -Recurse -File -Force -ErrorAction SilentlyContinue |
    Where-Object { $_.Extension -match '^\.jpe?g$' })
$jpegBytes = ($jpegFiles | Measure-Object -Property Length -Sum).Sum
if (-not $jpegBytes) {
    $jpegBytes = 0
}
$jpegGB = [math]::Round($jpegBytes / 1GB, 2)
Write-Host "jpeg     $($jpegFiles.Count) files, $jpegGB GB"
if ($jpegFiles.Count -eq 0) {
    throw 'No .jpg/.jpeg files found in the sample folder.'
}

# ---------------------------------------------------------------------------------------------
# Disk floor: read every JPEG byte once (what ImageDataHash needs at minimum)
# ---------------------------------------------------------------------------------------------

$diskFloorSeconds = $null
if (-not $SkipDiskFloor) {
    Write-Host ''
    Write-Host 'Disk floor: reading every JPEG once...'
    Clear-FileCache
    $stopwatch = [Diagnostics.Stopwatch]::StartNew()
    foreach ($file in $jpegFiles) {
        $stream = [IO.File]::OpenRead($file.FullName)
        try {
            $stream.CopyTo([IO.Stream]::Null, 1MB)
        }
        finally {
            $stream.Dispose()
        }
    }
    $stopwatch.Stop()
    $diskFloorSeconds = [math]::Round($stopwatch.Elapsed.TotalSeconds, 1)
    Write-Host "  $diskFloorSeconds s"
}

# ---------------------------------------------------------------------------------------------
# Runs
# ---------------------------------------------------------------------------------------------

$runs = New-Object System.Collections.Generic.List[object]
for ($round = 1; $round -le $Rounds; $round++) {
    foreach ($name in $selectedVariants) {
        Write-Host ''
        Write-Host "Round $round / $Rounds - $name"
        Clear-FileCache
        $result = Invoke-ExifTool $name $variantArgs[$name]
        Write-Host "  $($result.Seconds) s, exit $($result.ExitCode), $($result.JsonMB) MB json"
        $runs.Add([pscustomobject]@{
            Round    = $round
            Variant  = $name
            Seconds  = $result.Seconds
            ExitCode = $result.ExitCode
            JsonMB   = $result.JsonMB
        })
    }
}

# ---------------------------------------------------------------------------------------------
# Correctness: compare every variant with "old" on the fields imported into the DB
# ---------------------------------------------------------------------------------------------

Write-Host ''
Write-Host 'Comparing outputs...'

$parsed = @{}
foreach ($name in $selectedVariants) {
    $parsed[$name] = Read-ExifJson (Join-Path $OutDir "$name.json")
}

$lastRound = $runs | Where-Object { $_.Round -eq $Rounds }
$oldLast = $lastRound | Where-Object { $_.Variant -eq 'old' } | Select-Object -First 1

$comparison = New-Object System.Collections.Generic.List[object]
foreach ($name in $selectedVariants) {
    $records = $parsed[$name]
    $mismatches = 0
    $missingJpeg = 0
    $skippedNonJpeg = 0
    $extra = 0
    $diffLines = New-Object System.Collections.Generic.List[string]
    $fields = $jsonFields
    if ($name -eq 'nohash') {
        $fields = $jsonFields | Where-Object { $_ -ne 'File:ImageDataHash' }
    }

    if ($name -ne 'old' -and $parsed.ContainsKey('old')) {
        $oldRecords = $parsed['old']
        foreach ($sourceFile in $oldRecords.Keys) {
            $isJpeg = [IO.Path]::GetExtension($sourceFile) -match '^\.jpe?g$'
            if (-not $records.ContainsKey($sourceFile)) {
                if ($isJpeg) {
                    $missingJpeg++
                    if ($diffLines.Count -lt 20) {
                        $diffLines.Add("MISSING  $sourceFile")
                    }
                }
                else {
                    $skippedNonJpeg++
                }
                continue
            }
            foreach ($field in $fields) {
                $oldValue = ConvertTo-Canonical (Get-Field $oldRecords[$sourceFile] $field)
                $newValue = ConvertTo-Canonical (Get-Field $records[$sourceFile] $field)
                if ($oldValue -cne $newValue) {
                    $mismatches++
                    if ($diffLines.Count -lt 20) {
                        $diffLines.Add("DIFF     $sourceFile | $field | old=$oldValue | $name=$newValue")
                    }
                }
            }
        }
        foreach ($sourceFile in $records.Keys) {
            if (-not $oldRecords.ContainsKey($sourceFile)) {
                $extra++
                if ($diffLines.Count -lt 20) {
                    $diffLines.Add("EXTRA    $sourceFile")
                }
            }
        }
        [IO.File]::WriteAllLines((Join-Path $OutDir "$name.diff.txt"), [string[]]$diffLines, $utf8NoBom)
    }

    $run = $lastRound | Where-Object { $_.Variant -eq $name } | Select-Object -First 1
    $speedup = 'n/a'
    if ($oldLast -and $run.Seconds -gt 0) {
        $speedup = '{0:N2}x' -f ($oldLast.Seconds / $run.Seconds)
    }
    $errLines = @(Get-Content -LiteralPath (Join-Path $OutDir "$name.err") -ErrorAction SilentlyContinue).Count

    $comparison.Add([pscustomobject]@{
        Variant        = $name
        Records        = $records.Count
        Mismatches     = $(if ($name -eq 'old') { '-' } else { $mismatches })
        MissingJpeg    = $(if ($name -eq 'old') { '-' } else { $missingJpeg })
        SkippedNonJpeg = $(if ($name -eq 'old') { '-' } else { $skippedNonJpeg })
        Extra          = $(if ($name -eq 'old') { '-' } else { $extra })
        StderrLines    = $errLines
        SpeedupVsOld   = $speedup
    })
}

# ---------------------------------------------------------------------------------------------
# Report
# ---------------------------------------------------------------------------------------------

$report = New-Object System.Text.StringBuilder
[void]$report.AppendLine("exif benchmark - $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')")
[void]$report.AppendLine("exiftool       : $exifToolVersion ($ExifTool)")
[void]$report.AppendLine("powershell     : $($PSVersionTable.PSVersion)")
[void]$report.AppendLine("cpu cores      : $([Environment]::ProcessorCount)")
[void]$report.AppendLine("ram            : $ramGB GB")
[void]$report.AppendLine("sample         : $Sample")
[void]$report.AppendLine("jpeg files     : $($jpegFiles.Count) ($jpegGB GB)")
[void]$report.AppendLine("file cache     : $(if ($RamMap) { 'emptied before every run (RAMMap)' } else { 'not emptied: round 1 after the disk floor and later rounds read mostly from cache' })")
if ($null -ne $diskFloorSeconds) {
    $mbPerSecond = if ($diskFloorSeconds -gt 0) { [math]::Round(($jpegBytes / 1MB) / $diskFloorSeconds, 0) } else { 'n/a' }
    [void]$report.AppendLine("disk floor     : $diskFloorSeconds s ($mbPerSecond MB/s)")
}
[void]$report.AppendLine('')
[void]$report.AppendLine('Runs')
[void]$report.AppendLine(($runs | Format-Table -AutoSize | Out-String).TrimEnd())
[void]$report.AppendLine('')
[void]$report.AppendLine("Correctness vs old (fields: $($jsonFields -join ', ')) and speed-up of round $Rounds")
[void]$report.AppendLine(($comparison | Format-Table -AutoSize | Out-String).TrimEnd())
[void]$report.AppendLine('')
foreach ($row in $comparison) {
    if ($row.Records -eq 0) {
        [void]$report.AppendLine("WARNING: variant '$($row.Variant)' produced no records, its timing and comparison are not valid (see $($row.Variant).err).")
    }
}
foreach ($run in ($runs | Where-Object { $_.ExitCode -gt 1 })) {
    [void]$report.AppendLine("WARNING: variant '$($run.Variant)' round $($run.Round) exited with code $($run.ExitCode).")
}
[void]$report.AppendLine('')
foreach ($name in $selectedVariants) {
    $diffFile = Join-Path $OutDir "$name.diff.txt"
    if ((Test-Path -LiteralPath $diffFile) -and (Get-Item -LiteralPath $diffFile).Length -gt 0) {
        [void]$report.AppendLine("First differences for ${name}:")
        [void]$report.AppendLine((Get-Content -LiteralPath $diffFile -Encoding UTF8 | Out-String).TrimEnd())
        [void]$report.AppendLine('')
    }
    $errFile = Join-Path $OutDir "$name.err"
    if ((Test-Path -LiteralPath $errFile) -and (Get-Item -LiteralPath $errFile).Length -gt 0) {
        [void]$report.AppendLine("First stderr lines for ${name}:")
        [void]$report.AppendLine((Get-Content -LiteralPath $errFile -TotalCount 5 -Encoding UTF8 | Out-String).TrimEnd())
        [void]$report.AppendLine('')
    }
}

$resultsFile = Join-Path $OutDir 'results.txt'
[IO.File]::WriteAllText($resultsFile, $report.ToString(), $utf8NoBom)

Write-Host ''
Write-Host $report.ToString()
Write-Host "Saved to $resultsFile"
