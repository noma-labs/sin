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
        $notYetBorn = DB::connection('db_foto')
            ->table('photos_people')
            ->selectRaw('
                photos.id as photo_id,
                photos.file_name,
                photos.taken_at,
                p.id as persona_id,
                p.nome,
                p.cognome,
                p.data_nascita,
                p.data_decesso,
                DATEDIFF(photos.taken_at, p.data_nascita) as days_diff,
                "Non ancora nata" as issue_type
            ')
            ->join('photos', 'photos.id', '=', 'photos_people.photo_id')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id', '=', 'photos_people.persona_id')
            ->whereRaw('p.data_nascita IS NOT NULL')
            ->whereRaw('p.data_nascita > photos.taken_at');

        // Photos taken after person died
        $alreadyDeceased = DB::connection('db_foto')
            ->table('photos_people')
            ->selectRaw('
                photos.id as photo_id,
                photos.file_name,
                photos.taken_at,
                p.id as persona_id,
                p.nome,
                p.cognome,
                p.data_nascita,
                p.data_decesso,
                DATEDIFF(p.data_decesso, photos.taken_at) as days_diff,
                "Persona deceduta" as issue_type
            ')
            ->join('photos', 'photos.id', '=', 'photos_people.photo_id')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id', '=', 'photos_people.persona_id')
            ->whereRaw('p.data_decesso IS NOT NULL')
            ->whereRaw('photos.taken_at > p.data_decesso');

        $issues = $notYetBorn->union($alreadyDeceased)
            ->paginate(50);

        return view('photo.issues.index', compact('issues'));
    }
}
