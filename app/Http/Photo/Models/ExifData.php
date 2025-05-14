<?php

declare(strict_types=1);

namespace App\Photo\Models;

use Carbon\Carbon;
use Exception;
use Throwable;

final class ExifData
{
    public string $sha = '';

    public string $sourceFile = '';

    public ?string $mimeType = null;

    public string $fileName = '';

    public int $fileSize = 0;

    public string $directory = '';

    public string $folderTitle = '';

    public int $imageHeight = 0;

    public int $imageWidth = 0;

    public ?Carbon $takenAt = null;

    /** @var string[] */
    public array $subjects = [];

    public ?string $regionInfo = null;

    /**
     * @param array{
     *     "SourceFile"?: string,
     *     "FileName"?: string,
     *     "Directory"?: string,
     *     "FileSize"?: int,
     *     "FileType"?: string,
     *     "FileTypeExtension"?: string,
     *     "MIMEType"?: string,
     *     "CreateDate"?: string,
     *     "ImageWidth"?: int,
     *     "ImageHeight"?: int,
     *     "ImageDataHash"?: string,
     *     "Subject"?: array<int, string>,
     *     "System:FileName"?: string,
     *     "System:Directory"?: string,
     *     "System:FileSize"?: int,
     *     "File:FileType"?: string,
     *     "File:FileTypeExtension"?: string,
     *     "File:MIMEType"?: string,
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

        // GROUP-based name (using G1 option)
        if (isset($info['System:FileName'])) {
            $exif->fileName = $info['System:FileName'];
        } elseif (isset($info['FileName'])) {
            $exif->fileName = $info['FileName'];
        }

        if (isset($info['System:Directory'])) {
            $exif->directory = $info['System:Directory'];
        } elseif (isset($info['Directory'])) {
            $exif->directory = $info['Directory'];
        }

        if (isset($info['System:FileSize'])) {
            $exif->fileSize = $info['System:FileSize'];
        } elseif (isset($info['FileSize'])) {
            $exif->fileSize = $info['FileSize'];
        }

        if (isset($info['File:MIMEType'])) {
            $exif->mimeType = $info['File:MIMEType'];
        } elseif (isset($info['MIMEType'])) {
            $exif->mimeType = $info['MIMEType'];
        }

        if (isset($info['File:ImageWidth'])) {
            $exif->imageWidth = $info['File:ImageWidth'];
        } elseif (isset($info['ImageWidth'])) {
            $exif->imageWidth = $info['ImageWidth'];
        }

        if (isset($info['File:ImageHeight'])) {
            $exif->imageHeight = $info['File:ImageHeight'];
        } elseif (isset($info['ImageHeight'])) {
            $exif->imageHeight = $info['ImageHeight'];
        }

        if (isset($info['File:ImageDataHash'])) {
            $exif->sha = $info['File:ImageDataHash'];
        } elseif (isset($info['ImageDataHash'])) {
            $exif->sha = $info['ImageDataHash'];
        }

        if (isset($info['XMP-dc:Subject'])) {
            $exif->subjects = $info['XMP-dc:Subject'];
        } elseif (isset($info['Subject'])) {
            $exif->subjects = $info['Subject'];
        }

        if (isset($info['XMP-mwg-rs:RegionInfo'])) {
            try {
                $exif->regionInfo = json_encode($info['XMP-mwg-rs:RegionInfo']);
            } catch (Throwable) {
                // ignore
            }
        }

        // Date handling
        if (isset($info['XMP-xmp:CreateDate'])) {
            $dateString = $info['XMP-xmp:CreateDate'];
            try {
                $exif->takenAt = Carbon::createFromFormat('Y:m:d H:i:s', $dateString);
            } catch (Exception) {
                $exif->takenAt = null;
            }
        } elseif (isset($info['CreateDate'])) {
            $dateString = $info['CreateDate'];
            try {
                // Try with timezone
                $exif->takenAt = Carbon::createFromFormat('Y:m:d H:i:sP', $dateString);
            } catch (Exception) {
                try {
                    $exif->takenAt = Carbon::createFromFormat('Y:m:d H:i:s', $dateString);
                } catch (Exception) {
                    $exif->takenAt = null;
                }
            }
        } else {
            $exif->takenAt = null;
        }

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
            'subjects' => implode(',', $this->subjects),
            'region_info' => $this->regionInfo,
            'file_size' => $this->fileSize,
            'file_name' => $this->fileName,
            'mime_type' => $this->mimeType,
            'image_height' => $this->imageHeight,
            'image_width' => $this->imageWidth,
            'taken_at' => $this->takenAt,
            'directory' => $this->directory,
        ];
    }
}
