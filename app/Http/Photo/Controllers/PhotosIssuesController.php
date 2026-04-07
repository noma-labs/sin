<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\PhotoIssue;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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
                photos.source_file,
                photos.taken_at,
                photos.description,
                photos.location,
                p.id as persona_id,
                p.nome,
                p.cognome,
                p.data_nascita,
                p.data_decesso,
                dbf_all.datnum,
                dbf_all.anum,
                dbf_all.descrizione, as description
            ')
            ->leftJoin('photos', 'photos.id', '=', 'photos_issues.photo_id')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id', '=', 'photos_issues.persona_id')
            ->leftJoin('dbf_all', 'dbf_all.id', '=', 'photos.dbf_id')
            ->whereNull('photos_issues.resolved_at')
            ->oldest('photos.taken_at')
            ->paginate(1);

        return view('photo.issues.index', compact('issues'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'taken_at' => ['nullable', 'date'],
        ]);

        $issue = PhotoIssue::query()->findOrFail($id);

        if ($validated['taken_at'] ?? null) {
            $issue->photo->update(['taken_at' => $validated['taken_at']]);
        }

        return back()->with('success', 'Data foto aggiornata con successo.');
    }

    public function resolve(int $id): RedirectResponse
    {
        $issue = PhotoIssue::query()->findOrFail($id);
        $issue->update(['resolved_at' => now()]);

        return back()->with('success', 'Problema risolto con successo.');
    }
}
