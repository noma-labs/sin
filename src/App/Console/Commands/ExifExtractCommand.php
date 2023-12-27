<?php

namespace App\Console\Commands;

use Domain\Photo\Actions\ExtractExifAction;
use Domain\Photo\Actions\StoreExifIntoDBAction;
use Domain\Photo\Models\ExifData;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;

class ExifExtractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exif:extract 
                                    {path : The path (sub folder of the base app path) where the photos are} 
                                    {--save : Save the result into the database}}
                                    { --limit=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract the exif metadata from the photos and store them into the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('path');
        $saveToDb = $this->option('save');
        $limit = $this->option('limit');

        $fileName = (new ExtractExifAction())->execute($path);

        $this->info("Saving into $fileName");

        $raw_metadata = json_decode(file_get_contents($fileName), true);

        $photos = array();
        foreach ($raw_metadata as $metadata) {
            $data = ExifData::fromArray($metadata);
            $photos[] = $data;
        }
        if ($saveToDb) {
            (new StoreExifIntoDBAction())->execute($photos);
        }

        $this->table(
            ['Folder', 'file', 'Sha', 'Subjects', 'TakenAt'],
            collect($photos)
                ->take($limit)
                ->map(fn ($r) => [$r->folderTitle, $r->fileName, $r->sha, $r->getSubjects(), $r->takenAt])->toArray()
        );

        return Command::SUCCESS;

    }
}
