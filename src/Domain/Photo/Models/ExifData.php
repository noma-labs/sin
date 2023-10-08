<?php

namespace Domain\Photo\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ExifData
{
    public string $sha = '';

    public string $sourceFile = '';

    public ?string $fileType = null;

    public string $fileName = '';

    public int $fileSize = 0;

    public string $directory = '';

    public string $folderTitle = '';

    public string $fileExtension = '';

    public int $imageHeight = 0;

    public int $imageWidth = 0;

    public ?Carbon $takenAt = null;

    /** @var string[] */
    public array $subjects = [];

    public ?string $regionInfo = '{}';

    // TODO: exif tool export keywords in two types: string, and array of string.
    public string $keywords = '';

    public static function fromArray(array $info): self
    {
        $exif = new self();

        $exif->sourceFile = $info['SourceFile'];
        if (isset($info['FileName'])) {
            $exif->fileName = $info['FileName'];
        }
        if (isset($info['Directory'])) {
            $exif->imageWidth = $info['Directory'];
        }
        if (isset($info['FileSize'])) {
            $exif->imageWidth = $info['FileSize'];
        }
        if (isset($info['FileType'])) {
            $exif->fileType = $info['FileType'];
        }
        if (isset($info['FileTypeExtension'])) {
            $exif->fileType = $info['FileTypeExtension'];
        }
        if (isset($info['ImageWidth'])) {
            $exif->imageWidth = $info['ImageWidth'];
        }
        if (isset($info['ImageHeight'])) {
            $exif->imageHeight = $info['ImageHeight'];
        }
        $exif->folderTitle = Str::of($exif->directory)->basename();

        if (!isset($info['ImageDataHash'])) {
            $exif->sha = $info['ImageDataHash'];
        }

        // TODO: if taken at is missing ?
        // TODO: manage the timezone
        if (isset($info['CreateDate'])) {
            $exif->takenAt = Carbon::parse($info['CreateDate']);
        }
        if (isset($info['Subject'])) {
            $exif->subjects = $info['Subject'];
        }
        if (isset($info['RegionInfo'])) {
            $exif->regionInfo = json_encode($info['RegionInfo']);
        }

        return $exif;
    }
}
