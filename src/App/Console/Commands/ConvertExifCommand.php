<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Domain\Photo\Models\ExifData;
use Domain\Photo\Models\Photo;
use Illuminate\Console\Command;

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
            $exif->directory = $photo['Directory'];
            $exif->fileType = $photo['FileType'];

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
            //dd($exif);
            $raw->push($exif);
        }
        $chunks = $raw->chunk(500);
        foreach ($chunks as $chunk) {
            $attrs = [];
            foreach ($chunk as $r) {
                array_push($attrs, [
                    'sha' => $r->sha,
                    'source_file' => $r->sourceFile,
                    'subject' => implode(',', $r->subjects),
                    'taken_at' => $r->takenAt,
                    'file_name' => $r->fileName,
                    'directory' => $r->directory,
                ]);
            }
            Photo::insert($attrs);
        }

        return Command::SUCCESS;

    }
}
