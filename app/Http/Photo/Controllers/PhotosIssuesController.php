<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class PhotosIssuesController
{
    public function index(Request $request): View
    {
        $issues = DB::connection('db_foto')
            ->table('photos_issues')
            ->selectRaw('
                photos_issues.id,
                photos_issues.issue_type,
                photos_issues.photo_persona_name,
                photos.id as photo_id,
                photos.file_name,
                photos.taken_at,
                p.id as persona_id,
                p.nome,
                p.cognome,
                p.data_nascita,
                p.data_decesso,
                CASE
                    WHEN photos_issues.issue_type = "not_yet_born"
                        THEN DATEDIFF(photos.taken_at, p.data_nascita)
                    WHEN photos_issues.issue_type = "already_deceased"
                        THEN DATEDIFF(p.data_decesso, photos.taken_at)
                END as days_diff
            ')
            ->join('photos', 'photos.id', '=', 'photos_issues.photo_id')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id', '=', 'photos_issues.persona_id')
            ->whereNull('photos_issues.resolved_at')
            ->oldest('photos.taken_at')
            ->paginate(1);

        return view('photo.issues.index', compact('issues'));
    }
}
