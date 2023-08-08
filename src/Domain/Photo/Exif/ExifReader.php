<?php

namespace Domain\Photo\Exif;

use Symfony\Component\Process\Process;

class ExifReader
{
    protected $exifToolBinary = null;
    protected $sourcePath = null;
    protected $targetBasePath = null;

    public int $timeout;

    protected $additionalOptions = [];

    /**
     * @param string $filePath
     *
     * @return static
     */
    public static function file(string $file): static
    {
        return (new static())->setSourcePath($file);
    }

    public static function folder(string $folder): static
    {
        return (new static())->setSourcePath($folder)->recursively();
    }


    public function __construct()
    {
        $this->timeout = 10;
    }


    /**
     * @param null $sourcePath
     */
    public function setSourcePath($sourcePath): static
    {
        $this->sourcePath = $sourcePath;
        return $this;
    }

    public function setTargetBasePath($targetBasePath): static
    {
        $this->targetBasePath = $targetBasePath;
        return $this;
    }


    /**
     * @param string|null $exifToolBinary
     */
    public function setExifToolBinary(?string $exifToolBinary): void
    {
        $this->exifToolBinary = $exifToolBinary;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function enableStructuredInformation(): static
    {
        $this->additionalOptions[] = '-struct';
        return $this;
    }

    public function extractHashOfTheImage(): static
    {
        $this->additionalOptions[] = '-ImageDataHash';
        return $this;
    }

    public function allowDuplicates(): static
    {
        $this->additionalOptions[] = '-a';
        return $this;
    }

    public function disablePrintConversion(): static
    {
        $this->additionalOptions[] = '-n';
        return $this;
    }

    public function extractXMPInformation(string $subtag = null): static
    {
        $this->additionalOptions[] = $subtag ? "-xmp:" . $subtag : '-xmp:all';
        return $this;
    }

    public function exportToCSV(string $targetPath): static
    {
        $this->additionalOptions[] = $targetPath ? "-csv>" . $targetPath : '-csv';
        return $this;
    }

    public function exportToJSON(string $targetPath): static
    {
        $this->additionalOptions[] = $targetPath ? "-json>" . $targetPath : '-json';
        return $this;
    }

    public function recursively(): static
    {
        $this->additionalOptions[] = '-r';

        return $this;
    }

    public function saveCsv(string $targetPath)
    {
        $this->exportToCSV($targetPath);

        $command = $this->createExifToolCommand($this->sourcePath);

        $output = $this->callExifTool($command);

        echo($output);
    }

    public function saveJSON(string $fileName = null)
    {

        // if not given, it use the name of the source file
        $name = $fileName ?: pathinfo($this->sourcePath, PATHINFO_FILENAME) . '.json';

        // TODO: use a safer join path function
        $fullName = $this->targetBasePath . "/" . $name;
        $this->exportToJSON($fullName);

        $command = $this->createExifToolCommand($this->sourcePath);

        $output = $this->callExifTool($command);

        echo($output);
    }


    public function createExifToolCommand($targetPath = null): array
    {
        return array(
            "file" => $this->sourcePath,
            "options" => $this->additionalOptions
        );
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

        return $exifTool . " "
            . $optionsCommand . " "
            . $targetFile;

    }

    protected function getOptionsCommand(array $command): string
    {
        return implode(" ", $command['options']);
    }


}