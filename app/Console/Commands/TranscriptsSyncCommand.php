<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class TranscriptsSyncCommand extends Command
{
    protected $signature = 'transcripts:sync';

    protected $description = 'Sync recordings code from DATA/ORE columns and link transcripts to recordings by code match';

    public function handle(): int
    {
        $updated = DB::connection('archivio_nomadelfia')->update(<<<'SQL'
            UPDATE recordings
            SET code = CONCAT(
                DATE_FORMAT(`DATA`, '%Y%m%d'),
                CASE
                    WHEN `ORE` IS NULL OR TRIM(`ORE`) = '' THEN '00'
                    ELSE SUBSTRING(TRIM(`ORE`), 1, 3)
                END
            )
            WHERE `DATA` IS NOT NULL
        SQL);

        $this->info('Synced recordings code. Rows affected: '.$updated);

        $linked = DB::connection('archivio_nomadelfia')->update(<<<'SQL'
            UPDATE recording_transcripts rt
            INNER JOIN recordings r ON r.code = rt.code
            SET rt.recording_id = r.id
            WHERE rt.recording_id IS NULL AND r.code IS NOT NULL
        SQL);

        $this->info('Linked transcripts to recordings by code match. Rows affected: '.$linked);

        return self::SUCCESS;
    }
}
