<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class TranscriptsSyncCommand extends Command
{
    protected $signature = 'transcripts:sync';

    protected $description = 'Sync docx/mp3 files to recordings table by code extracted from file name or heading';

    public function handle(): int
    {
        $this->buildRecordingCode();
        $this->syncDocxFiles();
        $this->syncAudioFiles();

        return self::SUCCESS;
    }

    private function buildRecordingCode(): void
    {
        $updated = DB::connection('archivio_nomadelfia')->update(<<<'SQL'
            UPDATE recordings
            SET code = CONCAT(
                DATE_FORMAT(`DATA`, '%Y%m%d'),
                CASE
                    WHEN `ORE` IS NULL OR TRIM(`ORE`) = '' THEN '0A'
                    ELSE TRIM(`ORE`)
                END
            )
            WHERE `DATA` IS NOT NULL
        SQL);
        $this->info("Built recording code. Rows affected: {$updated}");
    }

    private function syncDocxFiles(): void
    {
        $linked = DB::connection('archivio_nomadelfia')->update(<<<'SQL'
            UPDATE recording_transcripts rt
            INNER JOIN recordings r ON r.code = rt.code
            SET rt.recording_id = r.id
            WHERE rt.recording_id IS NULL AND r.code IS NOT NULL
        SQL);

        $this->info("Linked transcripts to recordings by code match. Rows affected: {$linked}");
    }

    private function syncAudioFiles(): void
    {
        $linked = DB::connection('archivio_nomadelfia')->update(<<<'SQL'
            UPDATE recording_audio ra
            INNER JOIN recordings r ON r.code = ra.code
            SET ra.recording_id = r.id
            WHERE ra.recording_id IS NULL AND ra.code IS NOT NULL
        SQL);

        $this->info("Linked audio files to recordings by code match. Rows affected: {$linked}");
    }
}
