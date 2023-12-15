<?php

namespace Domain\Photo\Actions;

use Domain\Photo\Exif\ExifReader;
use Domain\Photo\Models\ExifData;

class ExtractExifAction
{
    /**
     * @input string $path
     *
     * @return ExifData[]
     */
    public function execute(string $path): array
    {
        /// - read the last inserted photo into the db and takes the 'taken at' data of the photo
        // - launch the exif reader with anf IF5 condition on the taken at data to read only photos after that data
        //  - save into the db the extract info
        // exiftool -r -n -a -struct -progress1000 -G1 -file:all  -exif:all -iptc:all -ImageDataHash -json>2000.json 2000
        $exifs = ExifReader::folder($path)
            ->disablePrintConversion()
            ->allowDuplicates()
            ->enableStructuredInformation()
            ->extractFileInformation()
            ->extractHashOfTheImage()
            ->extractXMPInformation()
//            ->verbose(0)
            ->savePhpArray();

        return $exifs->toArray();
    }
}
