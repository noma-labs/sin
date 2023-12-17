<?php

namespace App\Console\Commands;

use Domain\Photo\Actions\ExtractExifAction;
use Domain\Photo\Actions\StoreExifIntoDBAction;
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

        $took = Benchmark::measure(function () use ($path, $saveToDb, $limit) {

            $action = new (ExtractExifAction::class);
            $res = $action->execute(app()->basePath($path));

            if ($saveToDb) {
                $save = new (StoreExifIntoDBAction::class);
                $save->execute($res);
            }

            $this->table(
                ['Folder', 'FileName', 'Sha', 'Subjects', 'TakenAt'],
                collect($res)->take($limit)->map(fn ($r) => [$r->folderTitle, $r->fileName, $r->sha, $r->getSubjects(), $r->takenAt])->toArray()
            );
        });
        $this->info('Path: '.$path.' Took: '.$took);

        return Command::SUCCESS;

    }
}
