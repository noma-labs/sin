<?php

namespace Domain\Photo\Models;

use Carbon\Carbon;

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

    public ?string $regionInfo = '';

    // TODO: exif tool export keywords in two types: string, and array of string.
    public string $keywords = '';
}
