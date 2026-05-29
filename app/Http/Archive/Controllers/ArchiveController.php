<?php

declare(strict_types=1);

namespace App\Archive\Controllers;

use App\Archive\Models\Recording;

final class ArchiveController
{
    public function index()
    {
        $q = request('q', '');
        $selectedYear = request('year');
        $selectedGenere = request('genere');
        $selectedDocId = request('doc');

        $query = Recording::query();

        if ($selectedGenere) {
            $query->where('GENERE', $selectedGenere);
        }

        if (! empty($q)) {
            $query->join('recording_transcripts', 'recording_transcripts.recording_id', '=', 'recordings.id')
                ->whereRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE)', [$q]);
        }

        $countByDecade = (clone $query)
            ->whereNotNull('data')
            ->selectRaw('YEAR(data) as decade, COUNT(*) as count')
            ->groupBy('decade')
            ->orderBy('decade')
            ->get();

        $totalCount = (clone $query)->count();
        $genreOptions = collect();

        $genreQuery = (clone $query)->whereNotNull('GENERE')->where('GENERE', '!=', '');

        $query = $query
            ->with('transcript')
            ->select('recordings.id', 'recordings.data', 'recordings.AUTORE', 'recordings.DESTINATARI', 'recordings.GENERE', 'recordings.code', 'recordings.argomento');

        if ($selectedYear) {
            $query->whereYear('data', $selectedYear);
        }

        if (! empty($q)) {
            $query
                ->selectRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE) as relevance', [$q])
                ->orderByDesc('relevance');
        } else {
            $query->orderBy('data');
        }

        $filteredCount = (clone $query)->count();
        $transcripts = $query->limit(200)->get();

        if ($selectedYear) {
            $genreQuery->whereYear('data', $selectedYear);
        }

        $genreOptions = $genreQuery
            ->selectRaw('`GENERE`, COUNT(*) as count')
            ->groupBy('GENERE')
            ->orderByDesc('count')
            ->pluck('count', 'GENERE');

        $maxCount = $countByDecade->max('count') ?: 1;

        $selectedDoc = $selectedDocId ? $transcripts->firstWhere('id', $selectedDocId) : null;

        return view('archive.index', compact('countByDecade', 'transcripts', 'filteredCount', 'genreOptions', 'maxCount', 'totalCount', 'selectedYear', 'selectedDocId', 'selectedGenere', 'selectedDoc'));
    }

    public function show($id)
    {
        $transcript = Recording::with('transcript')->findOrFail($id);
        dd($transcript);

        return view('archive.show', ['transcript' => $transcript]);
    }
}
