<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Photo\Actions\StoreExifIntoDBAction;
use Illuminate\Console\Command;

final class ExifJsonImportCommand extends Command
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
     */
    public function handle(): int
    {
        $file = $this->argument('file');

        $this->info("Reading $file");

        $num = (new StoreExifIntoDBAction)->execute($file);

        $this->info("Inserted $num photos");

        return Command::SUCCESS;

    }
}
