<?php

namespace App\Console\Commands;

use Domain\Photo\Models\ExifData;
use Domain\Photo\Models\Photo;
use Illuminate\Console\Command;

class ExifImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exif:import {file: The json file to import}  {--base-path=storage_path() : the base_path }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a json file containing info extracted with exif:extract and save into database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file');
        $file = storage_path().$file;
        $photos = json_decode(file_get_contents($file), true);

        $raw = collect();
        foreach ($photos as $photo) {
            $data = ExifData::fromArray($photo);
            $raw->push($data);
        }
        $chunks = $raw->chunk(100);
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

        return Command::SUCCESS;
    }
}
