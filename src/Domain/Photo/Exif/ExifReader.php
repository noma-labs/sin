<?php

namespace Domain\Photo\Exif;

use Symfony\Component\Process\Process;

final class ExifReader
{
    protected $exifToolBinary = null;

    protected $sourcePath = null;

    protected $targetBasePath = null;

    public int $timeout;

    protected $additionalOptions = [];

    public function __construct()
    {
        $this->timeout = 180;
    }

    public static function file(string $file): ExifReader
    {
        return (new ExifReader())->setSourcePath($file);
    }

    public static function folder(string $folder): ExifReader
    {
        return (new ExifReader())->setSourcePath($folder)->recursively();
    }

    /**
     * @param  null  $sourcePath
     */
    public function setSourcePath($sourcePath): ExifReader
    {
        $this->sourcePath = $sourcePath;

        return $this;
    }

    public function setTargetBasePath($targetBasePath): ExifReader
    {
        $this->targetBasePath = $targetBasePath;

        return $this;
    }

    public function moveFileWithinFolder(): ExifReader
    {
        //  exiftool -d %Y/%m "-directory<filemodifydate" "-directory<createdate" "-directory<datetimeoriginal" /media/dido/LUMIX/DCIM/111_PANA
        $this->additionalOptions[] = '-d %Y/%m'; //  move into file structure with YYYY and month 01,02,04,..., 12

        return $this;
    }

    public function setExifToolBinary(?string $exifToolBinary): void
    {
        $this->exifToolBinary = $exifToolBinary;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function enableStructuredInformation(): ExifReader
    {
        $this->additionalOptions[] = '-struct';

        return $this;
    }

    public function extractHashOfTheImage(): ExifReader
    {
        $this->additionalOptions[] = '-ImageDataHash';

        return $this;
    }

    public function allowDuplicates(): ExifReader
    {
        $this->additionalOptions[] = '-a';

        return $this;
    }

    public function disablePrintConversion(): ExifReader
    {
        $this->additionalOptions[] = '-n';

        return $this;
    }

    public function extractFileInformation(string $subtag = null): ExifReader
    {
        $this->additionalOptions[] = $subtag ? '-file:'.$subtag : '-file:all';

        return $this;
    }

    public function extractXMPInformation(string $subtag = null): ExifReader
    {
        $this->additionalOptions[] = $subtag ? '-xmp:'.$subtag : '-xmp:all';

        return $this;
    }

    public function exportToCSV(string $targetPath): ExifReader
    {
        $this->additionalOptions[] = $targetPath ? '-csv>'.$targetPath : '-csv';

        return $this;
    }

    public function exportToJSON(string $targetPath): ExifReader
    {
        $this->additionalOptions[] = $targetPath ? '-json>'.$targetPath : '-json';

        return $this;
    }

    public function exportToPhp(): ExifReader
    {
        $this->additionalOptions[] = '-php';

        return $this;
    }

    public function recursively(): ExifReader
    {
        $this->additionalOptions[] = '-r';

        return $this;
    }

    public function saveCsv(string $targetPath)
    {
        $this->exportToCSV($targetPath);

        $command = $this->createExifToolCommand($this->sourcePath);

        $output = $this->callExifTool($command);

        echo $output;
    }

    public function saveJSON(string $fileName = null): string
    {
        // if not given, it use the name of the source file
        $name = $fileName ?: pathinfo($this->sourcePath, PATHINFO_FILENAME).'.json';

//         TODO: use a safer join pa'2023.json'th function
        $fullName = $this->targetBasePath.'/'.$name;
        $this->exportToJSON($fullName);

        $command = $this->createExifToolCommand($this->sourcePath);

        $output = $this->callExifTool($command);

//        echo $output;
        return $fullName;
    }

    public function savePhpArray(): array
    {
        $this->exportToPhp();

        $command = $this->createExifToolCommand($this->sourcePath);

        $output = $this->callExifTool($command);

        return eval('return '.$output);
    }

    public function createExifToolCommand($targetPath = null): array
    {
        return [
            'file' => $this->sourcePath,
            'options' => $this->additionalOptions,
        ];
    }

    protected function callExifTool(array $command): string
    {

        $fullCommand = $this->getFullCommand($command);

        $process = Process::fromShellCommandline($fullCommand)->setTimeout($this->timeout);

        $process->run();
        if ($process->isSuccessful()) {
            return rtrim($process->getOutput());
        }
        $process->clearOutput();
        $exitCode = $process->getExitCode();
        throw new \Exception($process->getErrorOutput());
    }

    protected function getFullCommand(array $command): string
    {
        $exifTool = $this->exifToolBinary ?: 'exiftool';
        $optionsCommand = $this->getOptionsCommand($command);
        $targetFile = $command['file'];

        return $exifTool.' '
            .$optionsCommand.' '
            .$targetFile;

    }

    protected function getOptionsCommand(array $command): string
    {
        return implode(' ', $command['options']);
    }
}
