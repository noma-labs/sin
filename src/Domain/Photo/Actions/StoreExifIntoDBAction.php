<?php

namespace Domain\Photo\Actions;

use Domain\Photo\Models\Photo;

class StoreExifIntoDBAction
{
    public function execute(array $exifs): void
    {
        $chunks = collect($exifs)->chunk(100);
        foreach ($chunks as $chunk) {
            $attrs = [];
            foreach ($chunk as $r) {
                $attrs[] = [
                    'uid' => uniqid(),
                    'sha' => $r->sha,
                    'source_file' => $r->sourceFile,
                    'subject' => implode(',', $r->subjects),
                    'folder_title' => $r->folderTitle,
                    'file_size' => $r->fileSize,
                    'file_name' => $r->fileName,
                    'file_type' => $r->fileType,
                    'file_type_extension' => $r->fileExtension,
                    'image_height' => $r->imageHeight,
                    'image_width' => $r->imageWidth,
                    'taken_at' => $r->takenAt,
                    'directory' => $r->directory,
                    'region_info' => $r->regionInfo,
                ];
            }
            Photo::insert($attrs);
        }
    }
}
