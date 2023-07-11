<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Domain\Photo\Models\ExifData;
use Domain\Photo\Models\Photo;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ConvertExifCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exif:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $photos = json_decode(file_get_contents(storage_path().'/json2022.json'), true);

        $raw = collect([]);
        foreach ($photos as $photo) {
            $exif = new ExifData();
            if (!isset($photo['ImageDataHash'])) {
                $this->error('ImageDataHash cannot be empty. ' . json_encode($photo));
                exit();
            }
            $exif->sha = $photo['ImageDataHash'];
            $exif->sourceFile = $photo['SourceFile'];
            $exif->fileName = $photo['FileName'];
            $exif->fileSize = $photo['FileSize'];
            $exif->fileType = $photo['FileType'];
            $exif->directory = $photo['Directory'];
            $exif->folderTitle = Str::of($exif->directory)->basename();
            $exif->fileType = $photo['FileType'];
            $exif->fileName = $photo['FileName'];
            $exif->fileExtension = $photo['FileTypeExtension'];
            if (isset($photo['ImageWidth'])) {
                $exif->imageWidth = $photo['ImageWidth'];
            }
            if (isset($photo['ImageHeight'])) {
                $exif->imageHeight = $photo['ImageHeight'];
            }

            // TODO: if taken at is missing ?
            // TODO: manage the timezone
            if (isset($photo['CreateDate'])) {
                $exif->takenAt = Carbon::parse($photo['CreateDate']);
            }
            if (isset($photo['Subject'])) {
                $exif->subjects = $photo['Subject'];
            }
            if (isset($photo['RegionInfo'])) {
                $exif->regionInfo = json_encode($photo['RegionInfo']);
            }
            $raw->push($exif);
        }
        $chunks = $raw->chunk(500);
        foreach ($chunks as $chunk) {
            $attrs = [];
            foreach ($chunk as $r) {
                array_push($attrs, [
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
                    'region_info' => $exif->regionInfo,
                ]);
            }
            Photo::insert($attrs);
        }

        return Command::SUCCESS;

    }
}
