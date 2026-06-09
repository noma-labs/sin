<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class TranscriptsImportMp3Command extends Command
{
    protected $signature = 'transcripts:import-mp3
                                {--truncate : Truncate table before import}
                                {--dry-run : Parse files without inserting rows}';

    protected $description = 'Import MP3 audio files from audio_originals disk into recording_audio table';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $audioDisk = Storage::disk('audio_originals');
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

        $mp3Files = collect($audioDisk->allFiles())
            ->filter(static fn (string $path): bool => str($path)->lower()->endsWith('.mp3'))
            ->values()
            ->all();

        sort($mp3Files);

        foreach ($mp3Files as $mp3Path) {
            if (! $audioDisk->exists($mp3Path)) {
                $skippedFiles++;

                continue;
            }

            $processedFiles++;

            try {
                $fileSizeBytes = $audioDisk->size($mp3Path);

                $batch[] = [
                    'file_name' => basename($mp3Path),
                    'file_path' => $mp3Path,
                    'file_size_bytes' => $fileSizeBytes,
                ];

                if (count($batch) >= $chunkSize) {
                    $insertedRows += $this->flushBatch($connection, $batch, $dryRun);
                    $batch = [];
                }
            } catch (Exception $e) {
                $this->error('Error processing '.basename($mp3Path).': '.$e->getMessage());
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
