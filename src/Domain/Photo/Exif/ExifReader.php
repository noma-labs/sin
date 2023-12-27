<?php

namespace Domain\Photo\Exif;

use Domain\Photo\Models\ExifData;
use Illuminate\Support\Collection;
use MongoDB\BSON\Iterator;
use Symfony\Component\Process\Process;

final class ExifReader
{
    protected $exifToolBinary = null;

    protected $sourcePath = null;

    protected $targetBasePath = null;

    public ?int $timeout;

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

    public function setTimeout(?int $timeout): ExifReader
    {
        $this->timeout = $timeout;

        return $this;
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

    public function verbose(int $level = 5): ExifReader
    {
        $this->additionalOptions[] = '-v'.$level;

        return $this;
    }

    public function extractFileInformation(?string $subtag = null): ExifReader
    {
        $this->additionalOptions[] = $subtag ? '-file:'.$subtag : '-file:all';

        return $this;
    }

    public function flatGroup1Tag(): ExifReader
    {
        // produce the tag group name in the key. Like  "File:ImageDataHash"
        $this->additionalOptions[] = '-G1';

        return $this;
    }

    public function extractXMPInformation(?string $subtag = null): ExifReader
    {
        $this->additionalOptions[] = $subtag ? '-xmp:'.$subtag : '-xmp:all';

        return $this;
    }

    public function extractExifInformation(?string $subtag = null): ExifReader
    {
        $this->additionalOptions[] = $subtag ? '-exif:'.$subtag : '-exif:all';

        return $this;
    }

    public function extractIPTCInformation(?string $subtag = null): ExifReader
    {
        $this->additionalOptions[] = $subtag ? '-iptc:'.$subtag : '-iptc:all';

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function fileOrder(string $tag, string $num = '4', string $order = 'ASC'): ExifReader
    {
        if (! in_array($order, ['ASC', 'DESC'])) {
            throw new \Exception('Order must be ASC or DESC');
        }
        if ($order === 'DESC') {
            $tag = '-'.$tag;
        }
        $this->additionalOptions[] = '-fileOrder'.$num.' '.$tag;

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

    public function saveJSON(?string $fileName = null): string
    {
        // if not given, it use the name of the source file
        $name = $fileName ?: pathinfo($this->sourcePath, PATHINFO_FILENAME).'.json';
        $fullName = $this->sourcePath.DIRECTORY_SEPARATOR.$name;
        $this->exportToJSON($fullName);

        $command = $this->createExifToolCommand($this->sourcePath);

        $output = $this->callExifTool($command);

        echo $output;

        return $fullName;
    }

    public function savePhpArray(): Collection
    {
        $this->exportToPhp();

        $command = $this->createExifToolCommand($this->sourcePath);

        $output = $this->callExifTool($command);

        $a = eval('return '.$output);

        return collect($a)->map(fn ($photo) => ExifData::fromArray($photo));
    }

    // TODO: the return iterator losse some row information
    public function run(): \Generator
    {
        $this->exportToPhp();

        $command = $this->createExifToolCommand($this->sourcePath);

        foreach (($this->callExifTool($command)) as $line) {
            yield $line;
        }

        return '';
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
        //        foreach ($process->getIterator() as $out) {
        //            dd($out);
        //        }
        //        $process->wait(function (string $code, string $data) {
        //            if ($code == Process::OUT) {
        ////                $data = str($data)
        ////                    ->replaceFirst("Array(Array(", "Array(");
        ////                    ->replaceFirst("))", ")")
        //
        ////                dd(str($data)->remove("\n"));
        ////                $data = str($data)
        ////                    ->replaceFirst("Array(Array(", "Array(");
        //////                    ->replaceFirst("))", ")")
        ////                $a = eval('return ' . $data);
        ////                dd($a);
        ////                $d = ExifData::fromArray($a);
        ////                dd($code, $d);
        //            yield $data;
        //        });

        if ($process->isSuccessful()) {
            return rtrim($process->getOutput());
        }
        $process->clearOutput();
        $exitCode = $process->getExitCode();

        return $process->getErrorOutput();
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
