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
        $selectedMonth = request('month');
        $selectedDocId = request('doc');
        $selectedGenere = request('genere');

        $countByDecadeQuery = Recording::selectRaw('YEAR(data) as decade, COUNT(*) as count')
            ->whereNotNull('data');

        if (! empty($q)) {
            $countByDecadeQuery->join('recording_transcripts', 'recording_transcripts.recording_id', '=', 'recordings.id')
                ->whereRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE)', [$q]);
        }

        if ($selectedGenere) {
            $countByDecadeQuery->where('GENERE', $selectedGenere);
        }

        $countByDecade = $countByDecadeQuery
            ->groupBy('decade')
            ->orderBy('decade')
            ->get();

        $totalCountQuery = Recording::query();
        if (! empty($q)) {
            $totalCountQuery->join('recording_transcripts', 'recording_transcripts.recording_id', '=', 'recordings.id')
                ->whereRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE)', [$q]);
        }
        if ($selectedGenere) {
            $totalCountQuery->where('GENERE', $selectedGenere);
        }
        $totalCount = $totalCountQuery->count();

        $selectedDocWords = collect();
        $genreOptions = collect();

        $query = Recording::with('transcript')
            ->select('recordings.id', 'recordings.data', 'recordings.AUTORE', 'recordings.DESTINATARI', 'recordings.GENERE', 'recordings.code', 'recordings.argomento');

        if ($selectedYear) {
            $query->whereYear('data', $selectedYear);
        }

        if ($selectedMonth) {
            $query->whereMonth('data', $selectedMonth);
        }

        if ($selectedGenere) {
            $query->where('GENERE', $selectedGenere);
        }

        if (! empty($q)) {
            $query->join('recording_transcripts', 'recording_transcripts.recording_id', '=', 'recordings.id')
                ->selectRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE) as relevance', [$q])
                ->whereRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE)', [$q])
                ->orderByDesc('relevance');
        } else {
            $query->orderBy('data');
        }

        // dd($query->toSql(), $query->getBindings());
        $filteredCount = (clone $query)->count();
        $transcripts = $query->limit(200)->get();


        // dd($transcripts->first());
        $genreQuery = Recording::whereNotNull('GENERE')->where('GENERE', '!=', '');

        if (! empty($q)) {
            $genreQuery->join('recording_transcripts', 'recording_transcripts.recording_id', '=', 'recordings.id')
                ->whereRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE)', [$q]);
        }

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

        return view('archive.index', compact('countByDecade', 'transcripts', 'filteredCount', 'genreOptions', 'maxCount', 'totalCount', 'selectedYear', 'selectedDocId', 'selectedMonth', 'selectedGenere', 'selectedDoc'));
    }

    public function show($id)
    {
        $transcript = Recording::with('transcript')->findOrFail($id);
        dd($transcript);

        return view('archive.show', ['transcript' => $transcript]);
    }
}
