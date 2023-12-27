<?php

namespace Domain\Photo\Actions;

use Domain\Photo\Exif\ExifReader;
use Domain\Photo\Models\ExifData;

class ExtractExifAction
{
    public function execute(string $path): string
    {
        // exiftool -r -n -a -struct -progress1000 -G1 -file:all -xmp:all  -exif:all -iptc:all -ImageDataHash  -json>2000.json 2000
        return ExifReader::folder($path)
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
            ->saveJSON();
    }
}
