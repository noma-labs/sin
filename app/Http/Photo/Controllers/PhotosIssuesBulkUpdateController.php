<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\PhotoIssue;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

final class PhotosIssuesBulkUpdateController
{
    public function index(Request $request): View
    {
        $openIssuesQuery = DB::connection('db_foto')
            ->table('photos_issues')
            ->leftJoin('photos', 'photos.id', '=', 'photos_issues.photo_id')
            ->leftJoin('dbf_all', 'dbf_all.id', '=', 'photos.dbf_id')
            ->whereNull('photos_issues.resolved_at')
            ->whereNotNull('dbf_all.datnum');

        $photosWithIssuesCount = (clone $openIssuesQuery)
            ->distinct()
            ->count('photos_issues.photo_id');

        $stripesWithIssuesCount = (clone $openIssuesQuery)
            ->distinct()
            ->count('dbf_all.datnum');

        $datnumGroups = (clone $openIssuesQuery)
            ->selectRaw('
                photos_issues.issue_type,
                dbf_all.datnum,
                MIN(dbf_all.id) as dbf_id,
                MIN(dbf_all.anum) as anum,
                MIN(dbf_all.descrizione) as dbf_descrizione,
                MIN(dbf_all.data) as strip_date,
                COUNT(photos_issues.id) as issue_count
            ')
            ->groupBy('photos_issues.issue_type', 'dbf_all.datnum')
            ->orderBy('dbf_all.datnum', 'asc')
            ->orderBy('photos_issues.issue_type', 'asc')
            ->paginate(1);

        /** @var stdClass|null $currentGroup */
        $currentGroup = $datnumGroups->first();

        $issues = $currentGroup
            ? DB::connection('db_foto')
                ->table('photos_issues')
                ->selectRaw('
                    photos_issues.id,
                    photos_issues.issue_type,
                    photos_issues.photo_persona_name,
                    photos_issues.note,
                    photos.id as photo_id,
                    photos.file_name,
                    photos.source_file,
                    photos.taken_at,
                    photos.description,
                    photos.location,
                    photos.dbf_id,
                    p.id as persona_id,
                    p.nome,
                    p.cognome,
                    p.data_nascita,
                    p.data_decesso,
                    dbf_all.datnum,
                    dbf_all.anum,
                    dbf_all.data as strip_date,
                    dbf_all.descrizione as dbf_descrizione
                ')
                ->leftJoin('photos', 'photos.id', '=', 'photos_issues.photo_id')
                ->leftJoin('db_nomadelfia.persone as p', 'p.id', '=', 'photos_issues.persona_id')
                ->leftJoin('dbf_all', 'dbf_all.id', '=', 'photos.dbf_id')
                ->whereNull('photos_issues.resolved_at')
                ->where('dbf_all.datnum', $currentGroup->datnum)
                ->where('photos_issues.issue_type', $currentGroup->issue_type)
                ->oldest('photos.taken_at')
                ->get()
            : collect();

        return view('photo.issues.bulk', compact(
            'datnumGroups',
            'issues',
            'photosWithIssuesCount',
            'stripesWithIssuesCount',
        ));
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'taken_at' => ['required', 'date'],
            'issue_ids' => ['required', 'array', 'min:1'],
            'issue_ids.*' => ['required', 'integer'],
        ]);

        /** @var \Illuminate\Database\Eloquent\Collection<int, PhotoIssue> $issues */
        $issues = PhotoIssue::query()
            ->with('photo')
            ->whereIn('id', $validated['issue_ids'])
            ->get();

        $resolvedAt = now();

        foreach ($issues as $issue) {
            /** @var PhotoIssue $issue */
            $oldDate = $issue->photo->taken_at
                ? $issue->photo->taken_at->format('Y-m-d')
                : 'null';
            $newDate = $validated['taken_at'];

            $issue->photo->update(['taken_at' => $newDate]);

            $dateMeta = "old_taken_at={$oldDate}|new_taken_at={$newDate}";
            $existing = $issue->note;
            $issue->update([
                'note' => $existing ? "{$existing}|{$dateMeta}" : $dateMeta,
                'resolved_at' => $resolvedAt,
            ]);
        }

        $count = $issues->count();

        return back()->with('success', "Data aggiornata per {$count} foto con successo.");
    }
}
