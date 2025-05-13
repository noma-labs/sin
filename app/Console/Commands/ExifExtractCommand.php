<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Photo\Actions\ExtractExifAction;
use App\Photo\Actions\StoreExifIntoDBAction;
use Illuminate\Console\Command;

final class ExifExtractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exif:extract
                                    {path : The path (sub folder of the base app path) where the photos are}
                                    {--save : Save the result into the database}
                                    {--exiftoolpath : The path to the exiftool binary (default: null)}
                                    { --limit=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract the exif metadata from the photos and store them into the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->argument('path');

        $exifBinPath = $this->option('exiftoolpath') ?? null;

        $saveToDb = $this->option('save');
        $limit = (int) $this->option('limit');

        $fileName = (new ExtractExifAction)->execute($path, $exifBinPath);

        $this->info("Saving into $fileName");

        if ($saveToDb) {
            $photos = (new StoreExifIntoDBAction)->execute($fileName);
            $this->table(['Folder', 'file', 'Sha', 'Subjects', 'TakenAt'], $photos->toArray());
        }

        return Command::SUCCESS;

    }
}
