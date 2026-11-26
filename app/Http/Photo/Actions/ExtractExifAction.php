<?php

declare(strict_types=1);

namespace App\Photo\Actions;

use App\Photo\Exif\ExifReader;
use App\Photo\Models\ExifData;

final class ExtractExifAction
{
    public function execute(string $path, ?string $exifToolBinPath): string
    {
        // exiftool -r -n -struct -G1 -e -System:FileName ... -XMP-xmp:CreateDate -json>"<path>/<name>.json" -- "<path>"
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

        return $reader->saveJSON();
    }
}
