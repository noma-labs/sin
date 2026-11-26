<?php

declare(strict_types=1);

namespace App\Photo\Actions;

use App\Photo\Exif\ExifReader;
use App\Photo\Models\ExifData;
use Illuminate\Support\Facades\Storage;

final class ExtractExifAction
{
    /**
     * Extract the exif of the photos in $path into storage/app/exif/<folder name>.json
     * and return the path of the JSON.
     */
    public function execute(string $path, ?string $exifToolBinPath): string
    {
        $disk = Storage::disk('local');
        $disk->makeDirectory('exif');
        $jsonFile = $disk->path('exif/'.basename($path).'.json');

        // exiftool -r -n -struct -G1 -e -System:FileName ... -XMP-xmp:CreateDate -json>"storage/app/exif/<name>.json" -- "<path>"
        $reader = ExifReader::folder($path)
            ->disablePrintConversion()
            ->enableStructuredInformation()
            ->flatGroup1Tag()
            ->disableCompositeTags()
            ->extractTags(ExifData::EXTRACTED_TAGS)
            ->setTimeout(null);
        if ($exifToolBinPath) {
            $reader->setExifToolBinary($exifToolBinPath);
        }

        return $reader->saveJSON($jsonFile);
    }
}
