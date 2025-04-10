<?php

declare(strict_types=1);

namespace Domain\Photo\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;

final class ExifData
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

    //    public ?string $regionInfo = '{}';

    // TODO: exif tool export keywords in two types: string, and array of string.
    public string $keywords = '';

    /**
     * @param array{
     *     "SourceFile"?: string,
     *     "FileName"?: string,
     *     "Directory"?: string,
     *     "FileSize"?: int,
     *     "FileType"?: string,
     *     "FileTypeExtension"?: string,
     *     "ImageWidth"?: int,
     *     "ImageHeight"?: int,
     *     "ImageDataHash"?: string,
     *     "Subject"?: array<int, string>,
     *     "System:FileName"?: string,
     *     "System:Directory"?: string,
     *     "System:FileSize"?: int,
     *     "File:FileType"?: string,
     *     "File:FileTypeExtension"?: string,
     *     "File:ImageWidth"?: int,
     *     "File:ImageHeight"?: int,
     *     "File:ImageDataHash"?: string,
     *     "XMP-dc:Subject"?: array<int, string>
     * } $info
     */
    public static function fromArray(array $info): self
    {
        $exif = new self;

        if (! isset($info['SourceFile'])) {
            throw new Exception("'SourceFile' not found in the exif data");
        }
        $exif->sourceFile = $info['SourceFile'];
        // TODO the date of the data pf the photo ??

        if (isset($info['FileName'])) {
            $exif->fileName = $info['FileName'];
        }
        if (isset($info['Directory'])) {
            $exif->directory = $info['Directory'];
        }
        if (isset($info['FileSize'])) {
            $exif->fileSize = $info['FileSize'];
        }
        if (isset($info['FileType'])) {
            $exif->fileType = $info['FileType'];
        }
        if (isset($info['FileTypeExtension'])) {
            $exif->fileExtension = $info['FileTypeExtension'];
        }
        if (isset($info['ImageWidth'])) {
            $exif->imageWidth = $info['ImageWidth'];
        }
        if (isset($info['ImageHeight'])) {
            $exif->imageHeight = $info['ImageHeight'];
        }
        if (isset($info['ImageDataHash'])) {
            $exif->sha = $info['ImageDataHash'];
        }
        if (isset($info['Subject'])) {
            $exif->subjects = $info['Subject'];
        }
        //        if (isset($info['RegionInfo'])) {
        //            $exif->regionInfo = json_encode($info['RegionInfo']);
        //        }

        // GROUP-based name (using G1 option)
        if (isset($info['System:FileName'])) {
            $exif->fileName = $info['System:FileName'];
        }
        if (isset($info['System:Directory'])) {
            $exif->directory = $info['System:Directory'];
        }
        if (isset($info['System:FileSize'])) {
            $exif->fileSize = $info['System:FileSize'];
        }
        if (isset($info['File:FileType'])) {
            $exif->fileType = $info['File:FileType'];
        }
        if (isset($info['File:FileTypeExtension'])) {
            $exif->fileExtension = $info['File:FileTypeExtension'];
        }
        if (isset($info['File:ImageWidth'])) {
            $exif->imageWidth = $info['File:ImageWidth'];
        }
        if (isset($info['File:ImageHeight'])) {
            $exif->imageHeight = $info['File:ImageHeight'];
        }
        if (isset($info['File:ImageDataHash'])) {
            $exif->sha = $info['File:ImageDataHash'];
        }
        if (isset($info['XMP-dc:Subject'])) {
            $exif->subjects = $info['XMP-dc:Subject'];
        }
        //        if (isset($info['XMP-mwg-rs:RegionInfo'])) {
        //            $exif->regionInfo = json_encode($info['XMP-mwg-rs:RegionInfo']);
        //        }

        $exif->folderTitle = Str::of($exif->directory)->basename()->toString();

        return $exif;
    }

    public function getSubjects(): string
    {
        return implode(',', $this->subjects);
    }

    /**
     * @return array<string, mixed>
     */
    public function toModelAttrs(): array
    {
        return [
            'uid' => uniqid(),
            'sha' => $this->sha,
            'source_file' => $this->sourceFile,
            'subject' => implode(',', $this->subjects),
            'folder_title' => $this->folderTitle,
            'file_size' => $this->fileSize,
            'file_name' => $this->fileName,
            'file_type' => $this->fileType,
            'file_type_extension' => $this->fileExtension,
            'image_height' => $this->imageHeight,
            'image_width' => $this->imageWidth,
            'taken_at' => $this->takenAt,
            'directory' => $this->directory,
            //            'region_info' => $this->regionInfo,
        ];
    }
}
