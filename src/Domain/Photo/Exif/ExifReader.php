<?php

declare(strict_types=1);

namespace Domain\Photo\Exif;

use Domain\Photo\Models\ExifData;
use Exception;
use Generator;
use Illuminate\Support\Collection;
use MongoDB\BSON\Iterator;
use Symfony\Component\Process\Process;

final class ExifReader
{
    public ?int $timeout;

    private ?string $exifToolBinary = null;

    private ?string $sourcePath = null;

    private array $additionalOptions = [];

    public function __construct()
    {
        $this->timeout = 180;
    }

    public static function file(string $file): self
    {
        return (new self)->setSourcePath($file);
    }

    public static function folder(string $folder): self
    {
        return (new self)->setSourcePath($folder)->recursively();
    }

    public function setSourcePath(string $sourcePath): self
    {
        $this->sourcePath = $sourcePath;

        return $this;
    }

    public function setTargetBasePath(): self
    {
        return $this;
    }

    public function moveFileWithinFolder(): self
    {
        //  exiftool -d %Y/%m "-directory<filemodifydate" "-directory<createdate" "-directory<datetimeoriginal" /media/dido/LUMIX/DCIM/111_PANA
        $this->additionalOptions[] = '-d %Y/%m'; //  move into file structure with YYYY and month 01,02,04,..., 12

        return $this;
    }

    public function setExifToolBinary(?string $exifToolBinary): void
    {
        $this->exifToolBinary = $exifToolBinary;
    }

    public function setTimeout(?int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function enableStructuredInformation(): self
    {
        $this->additionalOptions[] = '-struct';

        return $this;
    }

    public function extractHashOfTheImage(): self
    {
        $this->additionalOptions[] = '-ImageDataHash';

        return $this;
    }

    public function allowDuplicates(): self
    {
        $this->additionalOptions[] = '-a';

        return $this;
    }

    public function disablePrintConversion(): self
    {
        $this->additionalOptions[] = '-n';

        return $this;
    }

    public function verbose(int $level = 5): self
    {
        $this->additionalOptions[] = '-v'.$level;

        return $this;
    }

    public function extractFileInformation(?string $subtag = null): self
    {
        $this->additionalOptions[] = $subtag ? '-file:'.$subtag : '-file:all';

        return $this;
    }

    public function flatGroup1Tag(): self
    {
        // produce the tag group name in the key. Like  "File:ImageDataHash"
        $this->additionalOptions[] = '-G1';

        return $this;
    }

    public function extractXMPInformation(?string $subtag = null): self
    {
        $this->additionalOptions[] = $subtag ? '-xmp:'.$subtag : '-xmp:all';

        return $this;
    }

    public function extractExifInformation(?string $subtag = null): self
    {
        $this->additionalOptions[] = $subtag ? '-exif:'.$subtag : '-exif:all';

        return $this;
    }

    public function extractIPTCInformation(?string $subtag = null): self
    {
        $this->additionalOptions[] = $subtag ? '-iptc:'.$subtag : '-iptc:all';

        return $this;
    }

    /**
     * @throws Exception
     */
    public function fileOrder(string $tag, string $num = '4', string $order = 'ASC'): self
    {
        if (! in_array($order, ['ASC', 'DESC'])) {
            throw new Exception('Order must be ASC or DESC');
        }
        if ($order === 'DESC') {
            $tag = '-'.$tag;
        }
        $this->additionalOptions[] = '-fileOrder'.$num.' '.$tag;

        return $this;
    }

    public function exportToCSV(string $targetPath): self
    {
        $this->additionalOptions[] = $targetPath ? '-csv>'.$targetPath : '-csv';

        return $this;
    }

    public function exportToJSON(string $targetPath): self
    {
        $this->additionalOptions[] = $targetPath ? '-json>'.$targetPath : '-json';

        return $this;
    }

    public function exportToPhp(): self
    {
        $this->additionalOptions[] = '-php';

        return $this;
    }

    public function recursively(): self
    {
        $this->additionalOptions[] = '-r';

        return $this;
    }

    public function saveCsv(string $targetPath): void
    {
        $this->exportToCSV($targetPath);

        $command = $this->createExifToolCommand();

        $output = $this->callExifTool($command);

        echo $output;
    }

    public function saveJSON(?string $fileName = null): string
    {
        // if not given, it use the name of the source file
        $name = $fileName ?: pathinfo($this->sourcePath, PATHINFO_FILENAME).'.json';
        $fullName = $this->sourcePath.DIRECTORY_SEPARATOR.$name;
        $this->exportToJSON($fullName);

        $command = $this->createExifToolCommand();

        $this->callExifTool($command);

        return $fullName;
    }

    public function savePhpArray(): Collection
    {
        $this->exportToPhp();

        $command = $this->createExifToolCommand();

        $output = $this->callExifTool($command);

        $a = eval('return '.$output);

        return collect($a)->map(fn ($photo): \Domain\Photo\Models\ExifData => ExifData::fromArray($photo));
    }

    // TODO: the return iterator losse some row information
    public function run(): Generator
    {
        $this->exportToPhp();

        $command = $this->createExifToolCommand();

        yield $this->callExifTool($command);
        // foreach (($this->callExifTool($command)) as $line) {
        //     yield $line;
        // }

        // return '';
    }

    public function createExifToolCommand(): array
    {
        return [
            'file' => $this->sourcePath,
            'options' => $this->additionalOptions,
        ];
    }

    private function callExifTool(array $command): string
    {

        $fullCommand = $this->getFullCommand($command);

        $process = Process::fromShellCommandline($fullCommand)->setTimeout($this->timeout);
        $process->run();
        //        foreach ($process->getIterator() as $out) {
        //            dd($out);
        //        }
        //        $process->wait(function (string $code, string $data) {
        //            if ($code == Process::OUT) {
        // //                $data = str($data)
        // //                    ->replaceFirst("Array(Array(", "Array(");
        // //                    ->replaceFirst("))", ")")
        //
        // //                dd(str($data)->remove("\n"));
        // //                $data = str($data)
        // //                    ->replaceFirst("Array(Array(", "Array(");
        // ////                    ->replaceFirst("))", ")")
        // //                $a = eval('return ' . $data);
        // //                dd($a);
        // //                $d = ExifData::fromArray($a);
        // //                dd($code, $d);
        //            yield $data;
        //        });

        if ($process->isSuccessful()) {
            return rtrim($process->getOutput());
        }
        $process->clearOutput();
        $process->getExitCode();

        return $process->getErrorOutput();
    }

    private function getFullCommand(array $command): string
    {
        $exifTool = $this->exifToolBinary ?: 'exiftool';
        $optionsCommand = $this->getOptionsCommand($command);
        $targetFile = $command['file'];

        return $exifTool.' '
            .$optionsCommand.' '
            .$targetFile;

    }

    private function getOptionsCommand(array $command): string
    {
        return implode(' ', $command['options']);
    }
}
