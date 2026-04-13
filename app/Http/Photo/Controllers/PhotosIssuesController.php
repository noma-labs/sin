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
                dbf_all.descrizione as description
            ')
            ->leftJoin('photos', 'photos.id', '=', 'photos_issues.photo_id')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id', '=', 'photos_issues.persona_id')
            ->leftJoin('dbf_all', 'dbf_all.id', '=', 'photos.dbf_id')
            ->whereNull('photos_issues.resolved_at')
            ->orderBy('dbf_all.datnum', 'asc')
            ->paginate(1);

        return view('photo.issues.index', compact('issues'));
    }

    public function resolved(): View
    {
        $issues = DB::connection('db_foto')
            ->table('photos_issues')
            ->selectRaw('
                photos_issues.id,
                photos_issues.issue_type,
                photos_issues.photo_persona_name,
                photos_issues.resolved_at,
                photos_issues.note,
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
                dbf_all.descrizione as description
            ')
            ->leftJoin('photos', 'photos.id', '=', 'photos_issues.photo_id')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id', '=', 'photos_issues.persona_id')
            ->leftJoin('dbf_all', 'dbf_all.id', '=', 'photos.dbf_id')
            ->whereNotNull('photos_issues.resolved_at')
            ->orderBy('photos_issues.resolved_at', 'desc')
            ->paginate(25);

        $issues->through(fn (object $issue): object => $this->withParsedNote($issue));

        return view('photo.issues.resolved', compact('issues'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'taken_at' => ['nullable', 'date'],
        ]);

        $issue = PhotoIssue::query()->with('photo')->findOrFail($id);

        if ($validated['taken_at'] ?? null) {
            $oldDate = $issue->photo->taken_at
                ? $issue->photo->taken_at->format('Y-m-d')
                : 'null';
            $newDate = $validated['taken_at'];

            $issue->photo->update(['taken_at' => $newDate]);

            $dateMeta = "old_taken_at={$oldDate}|new_taken_at={$newDate}";
            $existing = $issue->note;
            $issue->update([
                'note' => $existing ? "{$existing}|{$dateMeta}" : $dateMeta,
            ]);
        }

        return back()->with('success', 'Data foto aggiornata con successo.');
    }

    public function resolve(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $issue = PhotoIssue::query()->findOrFail($id);

        $existing = $issue->note;
        $userNote = $validated['note'] ?: null;
        $note = collect([$existing, $userNote])->filter()->implode('|');

        $issue->update([
            'resolved_at' => now(),
            'note' => $note ?: null,
        ]);

        return back()->with('success', 'Problema risolto con successo.');
    }

    public function unresolve(int $id): RedirectResponse
    {
        $issue = PhotoIssue::query()->findOrFail($id);

        $issue->update([
            'resolved_at' => null,
        ]);

        return redirect()->route('photos.issues.index')->with('success', 'Problema riaperto con successo.');
    }

    private function withParsedNote(object $issue): object
    {
        $note = $issue->note ?? null;
        $parts = $note ? explode('|', $note) : [];
        $dateChanges = [];
        $plainNotes = [];
        $pendingOld = null;

        foreach ($parts as $part) {
            if (str_starts_with($part, 'old_taken_at=')) {
                $pendingOld = substr($part, strlen('old_taken_at='));
            } elseif (str_starts_with($part, 'new_taken_at=') && $pendingOld !== null) {
                $dateChanges[] = ['from' => $pendingOld, 'to' => substr($part, strlen('new_taken_at='))];
                $pendingOld = null;
            } elseif (trim($part) !== '') {
                $plainNotes[] = $part;
            }
        }

        $issue->date_changes = $dateChanges;
        $issue->plain_notes = $plainNotes;

        return $issue;
    }
}
