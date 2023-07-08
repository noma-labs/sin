<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class Exif
{

    public string $sha = '';
    public string $sourceFile = '';
    public string $fileName = '';

    public ?string $fileType = null;

    public ?Carbon $takenAt = null;
    /** @var string[] */
    public array $subjects = [];
}

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

        $p = collect([]);

        $raw = collect([]);
        foreach ($photos as $photo) {
            $exif = new Exif();
            $exif->sha = $photo['ImageDataHash'];
            $exif->sourceFile = $photo['SourceFile'];
            $exif->fileName = $photo['FileName'];
            $exif->fileType = $photo['FileType'];
            if (isset($photo['CreateDate'])) {
                $exif->takenAt = Carbon::parse($photo['CreateDate']);
            }
            if (isset($photo['Subject'])) {
                $exif->subjects = $photo['Subject'];
            }
            $raw->push($exif);
        }
        $chunks = $raw->chunk(100);
        attrs = [];
        foreach ($chunks as $chunk) {
            
            query = "INSERT INTO photos (sha, source_file, subjects)"
        }

        dd($raw->take(10)->toArray());

        return Command::SUCCESS;


    }
}
