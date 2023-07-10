<?php

namespace Domain\Photo\Models;

use Carbon\Carbon;

class ExifData
{

    public string $sha = '';
    public string $sourceFile = '';
    public string $fileName = '';

    public ?string $fileType = null;

    public ?Carbon $takenAt = null;
    /** @var string[] */
    public array $subjects = [];

}