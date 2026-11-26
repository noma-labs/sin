<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Photo\Actions\ExtractExifAction;
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
                                    {--exiftoolpath= : The path to the exiftool binary (default: null)}
                                    {--php-hash : Compute File:ImageDataHash in PHP instead of exiftool (faster)}
                                    { --limit=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract the exif metadata from the photos and store them into the database';

    public function handle(): int
    {
        $path = $this->argument('path');

        $exifBinPath = $this->option('exiftoolpath');
        if (is_array($exifBinPath)) {
            $this->error('--exiftoolpath cannot be an array.');

            return Command::FAILURE;
        }
        $exifBinPath = $exifBinPath !== null ? (string) $exifBinPath : null;

        $limit = (int) $this->option('limit');

        $start = microtime(true);
        $fileName = (new ExtractExifAction)->execute($path, $exifBinPath, (bool) $this->option('php-hash'));
        $seconds = round(microtime(true) - $start, 1);

        $this->info("Saved into $fileName in $seconds s");

        return Command::SUCCESS;

    }
}
