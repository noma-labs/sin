<?php

declare(strict_types=1);

namespace App\Photo\Actions;

use App\Photo\Exif\ExifReader;
use App\Photo\Exif\JpegImageDataHash;
use App\Photo\Models\ExifData;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class ExtractExifAction
{
    private const IMAGE_DATA_HASH = 'File:ImageDataHash';

    /**
     * Extract the exif of the photos in $path into storage/app/exif/<folder name>.json
     * and return the path of the JSON.
     *
     * With $hashInPhp, exiftool doesn't compute File:ImageDataHash (~80% of its time):
     * JpegImageDataHash adds the same hash to the JSON afterwards.
     */
    public function execute(string $path, ?string $exifToolBinPath, bool $hashInPhp = false): string
    {
        $disk = Storage::disk('local');
        $disk->makeDirectory('exif');
        $jsonFile = $disk->path('exif/'.basename($path).'.json');

        $tags = $hashInPhp
            ? array_values(array_diff(ExifData::EXTRACTED_TAGS, [self::IMAGE_DATA_HASH]))
            : ExifData::EXTRACTED_TAGS;

        // exiftool -r -n -struct -G1 -e -System:FileName ... -XMP-xmp:CreateDate -json>"storage/app/exif/<name>.json" -- "<path>"
        $reader = ExifReader::folder($path)
            ->disablePrintConversion()
            ->enableStructuredInformation()
            ->flatGroup1Tag()
            ->disableCompositeTags()
            ->extractTags($tags)
            ->setTimeout(null);
        if ($exifToolBinPath) {
            $reader->setExifToolBinary($exifToolBinPath);
        }
        $reader->saveJSON($jsonFile);

        if ($hashInPhp) {
            $this->addImageDataHash($jsonFile);
        }

        return $jsonFile;
    }

    /**
     * Rewrite the JSON one photo at a time with the hash of each JPEG, like exiftool would
     * have written it (no hash for the files that aren't a JPEG).
     */
    private function addImageDataHash(string $jsonFile): void
    {
        if (! File::exists($jsonFile) || File::size($jsonFile) === 0) {
            return;
        }

        $hashedFile = $jsonFile.'.tmp';
        $output = fopen($hashedFile, 'wb');
        if ($output === false) {
            throw new RuntimeException("Cannot write $hashedFile");
        }

        try {
            $separator = '[';
            foreach (new JsonParser($jsonFile) as $exif) {
                if (is_array($exif) && is_string($exif['SourceFile'] ?? null)) {
                    $hash = JpegImageDataHash::ofFile($exif['SourceFile']);
                    if ($hash !== null) {
                        $exif[self::IMAGE_DATA_HASH] = $hash;
                    }
                }
                fwrite($output, $separator.json_encode($exif, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
                $separator = ",\n";
            }
            fwrite($output, $separator === '[' ? '[]' : ']');
        } finally {
            fclose($output);
        }

        File::move($hashedFile, $jsonFile);
    }
}
