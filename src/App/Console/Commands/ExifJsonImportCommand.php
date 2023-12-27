<?php

namespace App\Console\Commands;

use Domain\Photo\Actions\ExtractExifAction;
use Domain\Photo\Actions\StoreExifIntoDBAction;
use Illuminate\Console\Command;

class ExifJsonImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exif:import 
                                    {file : The path (sub folder of the base app path) where the json file} 
                                    { --limit=10} ';

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
        $file = $this->argument('file');
        $limit = $this->option('limit');

        $this->info("Reading $file");

        $photos = (new StoreExifIntoDBAction())->execute($file);

        $this->table(
            ['Folder', 'file', 'Sha', 'Subjects', 'TakenAt'],
            collect($photos)
                ->take($limit)
                ->map(fn ($r) => [$r->folderTitle, $r->fileName, $r->sha, $r->getSubjects(), $r->takenAt])->toArray()
        );

        return Command::SUCCESS;

    }
}
