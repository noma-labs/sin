<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class TranscriptsImportMp3Command extends Command
{
    protected $signature = 'transcripts:import-mp3
                                {path : Root folder containing year-based subfolders (e.g., 1940-1981)}
                                {--truncate : Truncate table before import}
                                {--dry-run : Parse files without inserting rows}';

    protected $description = 'Import MP3 audio files from year-based folder structure into recording_audio table';

    public function handle(): int
    {
        $rootPath = (string) $this->argument('path');
        $dryRun = (bool) $this->option('dry-run');

        if (! is_dir($rootPath)) {
            $this->error("Directory not found: {$rootPath}");

            return self::FAILURE;
        }

        $connection = DB::connection('archivio_nomadelfia');

        if ((bool) $this->option('truncate') && ! $dryRun) {
            $connection->statement('SET FOREIGN_KEY_CHECKS=0');
            $connection->table('recording_audio')->truncate();
            $connection->statement('SET FOREIGN_KEY_CHECKS=1');
            $this->warn('Table recording_audio truncated.');
        }

        $batch = [];
        $insertedRows = 0;
        $processedFiles = 0;
        $skippedFiles = 0;
        $chunkSize = 500;

        // Scan year folders: 1949, 1950, ..., 1981
        $yearFolders = array_filter(
            scandir($rootPath),
            static fn (string $folder): bool => is_dir("{$rootPath}/{$folder}") && preg_match('/^\d{4}$/', $folder)
        );

        sort($yearFolders);

        foreach ($yearFolders as $yearFolder) {
            $yearPath = "{$rootPath}/{$yearFolder}";

            // Scan MP3 files in year folder
            $mp3Files = array_filter(
                scandir($yearPath) ?: [],
                static fn (string $file): bool => mb_strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'mp3'
            );

            foreach ($mp3Files as $mp3File) {
                $filePath = "{$yearPath}/{$mp3File}";

                if (! file_exists($filePath)) {
                    $skippedFiles++;

                    continue;
                }

                $processedFiles++;

                try {
                    $fileSize = (float) filesize($filePath) / (1024 * 1024); // Convert bytes to MB

                    $batch[] = [
                        'file_name' => $mp3File,
                        'file_path' => $filePath,
                        'file_size_mb' => $fileSize,
                    ];

                    if (count($batch) >= $chunkSize) {
                        $insertedRows += $this->flushBatch($connection, $batch, $dryRun);
                        $batch = [];
                    }
                } catch (Exception $e) {
                    $this->error("Error processing {$mp3File}: {$e->getMessage()}");
                }
            }
        }

        if ($batch !== []) {
            $insertedRows += $this->flushBatch($connection, $batch, $dryRun);
        }

        $mode = $dryRun ? 'dry-run parsed' : 'inserted';
        $this->info("{$mode} MP3 files: {$insertedRows}");
        $this->line("processed files: {$processedFiles}");
        $this->line("skipped files: {$skippedFiles}");

        return self::SUCCESS;
    }

    /**
     * @param  array<int, array<string, mixed>>  $batch
     */
    private function flushBatch(object $connection, array $batch, bool $dryRun): int
    {
        if ($batch === []) {
            return 0;
        }

        if (! $dryRun) {
            $connection->table('recording_audio')->insert($batch);
        }

        return count($batch);
    }
}
