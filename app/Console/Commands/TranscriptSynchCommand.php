<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class TranscriptSynchCommand extends Command
{
    protected $signature = 'transcripts:sync';

    protected $description = 'Sync recordings code with format YYYYMMDDORE from DATA and ORE columns (up to 11 chars)';

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
        return self::SUCCESS;
    }
}