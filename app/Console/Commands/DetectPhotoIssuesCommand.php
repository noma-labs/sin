<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class DetectPhotoIssuesCommand extends Command
{
    protected $signature = 'photos:detect-issues {--dry-run : Run without persisting to database}';

    protected $description = 'Detect photos with chronological issues and persist them to the photos_issues table';

    public function handle(): int
    {
        $this->info('Detecting photo issues...');

        $notYetBorn = DB::connection('db_foto')
            ->table('photos_people')
            ->selectRaw('
                photos.id as photo_id,
                photos_people.persona_id,
                photos_people.persona_nome as photo_persona_nome,
                p.nome as actual_persona_nome,
                "not_yet_born" as issue_type
            ')
            ->join('photos', 'photos.id', '=', 'photos_people.photo_id')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id', '=', 'photos_people.persona_id')
            ->whereRaw('p.data_nascita IS NOT NULL')
            ->whereRaw('p.data_nascita > photos.taken_at')
            ->get();

        $alreadyDeceased = DB::connection('db_foto')
            ->table('photos_people')
            ->selectRaw('
                photos.id as photo_id,
                photos_people.persona_id,
                photos_people.persona_nome as photo_persona_nome,
                p.nome as actual_persona_nome,
                "already_deceased" as issue_type
            ')
            ->join('photos', 'photos.id', '=', 'photos_people.photo_id')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id', '=', 'photos_people.persona_id')
            ->whereRaw('p.data_decesso IS NOT NULL')
            ->whereRaw('photos.taken_at > p.data_decesso')
            ->get();

        $issues = $notYetBorn->merge($alreadyDeceased);

        $now = now()->toDateTimeString();

        $rows = $issues->map(fn ($issue) => [
            'photo_id' => $issue->photo_id,
            'persona_id' => $issue->persona_id,
            'issue_type' => $issue->issue_type,
            'photo_persona_name' => $issue->photo_persona_nome ?: $issue->actual_persona_nome,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        $insertedChronological = 0;
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN: Not persisting to database');
        } else {
            $insertedChronological = DB::connection('db_foto')
                ->table('photos_issues')
                ->insertOrIgnore($rows);
        }

        $this->info(sprintf('%d chronological issues detected: %d inserted, %d skipped.', count($rows), $insertedChronological, count($rows) - $insertedChronological));

        $wrongYear = DB::connection('db_foto')
            ->select('
                SELECT p.id AS photo_id
                FROM photos p
                JOIN dbf_all d ON p.dbf_id = d.id
                WHERE
                    p.taken_at IS NOT NULL
                    AND d.descrizione REGEXP \'19[2-7][0-9]|1990\'
                    AND YEAR(p.taken_at) != CAST(REGEXP_SUBSTR(d.descrizione, \'19[2-7][0-9]|1990\') AS UNSIGNED)
                    AND d.tp IN (\'RA\', \'RB\', \'RD\', \'RS\')
                    AND p.id NOT IN (SELECT photo_id FROM photos_issues)
            ');

        $wrongYearRows = array_map(fn ($row) => [
            'photo_id' => $row->photo_id,
            'persona_id' => null,
            'issue_type' => 'year_mismatch_description',
            'created_at' => $now,
            'updated_at' => $now,
        ], $wrongYear);

        $insertedWrongYear = 0;
        if (! $this->option('dry-run')) {
            $insertedWrongYear = DB::connection('db_foto')
                ->table('photos_issues')
                ->insertOrIgnore($wrongYearRows);
        }

        $this->info(sprintf('%d year_mismatch_description issues detected: %d inserted, %d skipped.', count($wrongYearRows), $insertedWrongYear, count($wrongYearRows) - $insertedWrongYear));

        $totalInserted = $insertedChronological + $insertedWrongYear;
        $totalSkipped = (count($rows) + count($wrongYearRows)) - $totalInserted;
        $this->info(sprintf('Done. %d total detected, %d inserted, %d skipped.', count($rows) + count($wrongYearRows), $totalInserted, $totalSkipped));

        return self::SUCCESS;
    }
}
