<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class DetectPhotoIssuesCommand extends Command
{
    protected $signature = 'photos:detect-issues';

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

        DB::connection('db_foto')
            ->table('photos_issues')
            ->insertOrIgnore($rows);

        $this->info(sprintf('Done. %d issues detected.', count($rows)));

        return self::SUCCESS;
    }
}
