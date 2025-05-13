<?php

declare(strict_types=1);

namespace App\Photo\Actions;

use App\Photo\Exif\ExifReader;

final class ExtractExifAction
{
    public function execute(string $path, ?string $exifToolBinPath): string
    {
        // exiftool -r -n -a -struct -progress1000 -G1 -file:all -xmp:all  -exif:all -iptc:all -ImageDataHash  -json>2000.json 2000
        $reader = ExifReader::folder($path)
            ->setExifToolBinary()
            ->disablePrintConversion()
            ->allowDuplicates()
            ->enableStructuredInformation()
            ->flatGroup1Tag()
            ->extractFileInformation()
            ->extractXMPInformation()
            ->extractExifInformation()
            ->extractIPTCInformation()
            ->extractHashOfTheImage()
            ->setTimeout(null);
        if ($exifToolBinPath){
            $reader->setExifToolBinary($exifToolBinPath);
        }

        return $reader->saveJSON();
    }
}
