<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Photo\Exif\JpegImageDataHash;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Console\Command;

final class ExifCheckHashCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exif:check-hash
                                    {file : An exiftool JSON with File:ImageDataHash, like the one of exif:extract}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute the image hash of the photos of an exiftool JSON in PHP and compare it with the one of exiftool';

    public function handle(): int
    {
        $jsonFile = $this->argument('file');
        if (! is_string($jsonFile)) {
            $this->error('The file must be a string.');

            return self::FAILURE;
        }

        $photos = 0;
        $megabytes = 0.0;
        /** @var list<array{string, string, string}> $mismatches */
        $mismatches = [];
        /** @var list<string> $missing */
        $missing = [];
        $start = microtime(true);

        foreach (new JsonParser($jsonFile) as $exif) {
            $file = is_array($exif) ? ($exif['SourceFile'] ?? null) : null;
            $exiftoolHash = is_array($exif) ? ($exif['File:ImageDataHash'] ?? null) : null;
            if (! is_string($file) || ! is_string($exiftoolHash)) {
                continue;
            }
            if (! is_file($file)) {
                $missing[] = $file;

                continue;
            }
            $photos++;
            $megabytes += filesize($file) / 1_000_000;
            $hash = JpegImageDataHash::ofFile($file);
            if ($hash !== $exiftoolHash) {
                $mismatches[] = [$file, $exiftoolHash, $hash ?? '-'];
            }
        }

        $seconds = microtime(true) - $start;
        $this->table(['Photos', 'Mismatches', 'Missing files', 'MB', 'Seconds', 'MB/s'], [[
            $photos,
            count($mismatches),
            count($missing),
            round($megabytes),
            round($seconds, 1),
            $seconds > 0 ? round($megabytes / $seconds, 1) : '-',
        ]]);

        if ($mismatches !== []) {
            $this->table(['File', 'exiftool', 'PHP'], array_slice($mismatches, 0, 20));
        }
        if ($missing !== []) {
            $this->warn('Files not found (first 20):');
            foreach (array_slice($missing, 0, 20) as $file) {
                $this->line($file);
            }
        }

        return $mismatches === [] ? self::SUCCESS : self::FAILURE;
    }
}
