<?php

namespace Domain\Photo\Actions;

use Domain\Photo\Models\ExifData;
use Illuminate\Support\Facades\DB;

class StoreExifIntoDBAction
{
    /**
     * @param string $jsonFile
     */
    public function execute(string $jsonFile): array
    {
        $raw_metadata = json_decode(file_get_contents($jsonFile), true);

        $photos = array();
        foreach ($raw_metadata as $metadata) {
            $data = ExifData::fromArray($metadata);
            $photos[] = $data;
        }
        $chunks = collect($photos)->chunk(100);

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
            DB::connection('db_foto')->table('photos')->insert($attrs);
        }
        return $photos;
    }
}
