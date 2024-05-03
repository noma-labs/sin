<?php

namespace Domain\Photo\Exif;

use Domain\Photo\Models\ExifData;

interface Driver
{
    public function getExifData(string $path): ExifData;
}

final class PhpExifDriver implements Driver
{
    public function getExifData(string $path): ExifData
    {
        $info = exif_read_data($path);

        return new ExifData($info);
    }
}

final class ExifToolDriver implements Driver
{
    public function getExifData(string $path): ExifData
    {
        $data = ExifReader::file($path)
            ->disablePrintConversion()
            ->allowDuplicates()
            ->enableStructuredInformation()
            ->flatGroup1Tag()
            ->extractFileInformation()
            ->extractXMPInformation()
            ->extractExifInformation()
            ->extractIPTCInformation()
            ->extractHashOfTheImage()
            ->setTimeout(null)
            ->savePhpArray();

        return $data;
    }
}

class Image
{
    private Driver $driver;

    private string $path;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function load(string $path): Image
    {
        $this->path = $path;

        return $this;
    }

    public function getExifData(string $path): ExifData
    {
        return $this->driver->getExifData($path);
    }
}
