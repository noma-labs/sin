<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Domain\Photo\Models\ExifData;
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
        $photos = json_decode(file_get_contents(storage_path() . "/exif-photos-all.json"), true);

        $raw = collect([]);
        foreach ($photos as $photo) {
            $exif = new ExifData();
            $exif->sha = $photo['ImageDataHash'];
            $exif->sourceFile = $photo['SourceFile'];
            $exif->fileName = $photo['FileName'];
            $exif->fileType = $photo['FileType'];
            // TODO: if taken at is missing ?
            // manage the timezone
            if (isset($photo['CreateDate'])) {
                $exif->takenAt = Carbon::parse($photo['CreateDate']);
            }
            if (isset($photo['Subject'])) {
                $exif->subjects = $photo['Subject'];
            }
            $raw->push($exif);
        }
        $chunks = $raw->chunk(100);
        var attrs = array([]);
        foreach ($chunks as $chunk) {
            array_push(attrs, [
                "sha"=>$chunk->sha,
                "source_file" => $chunk->sourceFile
            ]);
            query = "INSERT INTO photos (sha, source_file, subjects)"
        }

        dd($raw->take(10)->toArray());

        return Command::SUCCESS;


    }
}
