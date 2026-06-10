<?php

declare(strict_types=1);

namespace App\Archive\Controllers;

use App\Archive\Models\Recording;
use App\Archive\Models\RecordingAudio;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ArchiveController
{
    public function index()
    {
        $q = request('q', '');
        $selectedYear = request('year');
        $selectedGenere = request('genere');
        $selectedDocId = request('doc');

        // Execute MATCH once to get filtered IDs
        $matchQuery = Recording::query();

        if ($selectedGenere) {
            $matchQuery->where('GENERE', $selectedGenere);
        }

        if (! empty($q)) {
            $matchQuery->join('recording_transcripts', 'recording_transcripts.recording_id', '=', 'recordings.id')
                ->whereRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE)', [$q])
                ->distinct()
                ->select('recordings.id');
        }

        $filteredIds = $matchQuery->pluck('id')->toArray();

        // Build base query using filtered IDs (no MATCH needed)
        $baseQuery = Recording::whereIn('recordings.id', $filteredIds);

        $countByDecade = (clone $baseQuery)
            ->whereNotNull('data')
            ->selectRaw('YEAR(data) as decade, COUNT(*) as count')
            ->groupBy('decade')
            ->orderBy('decade')
            ->get();

        $totalCount = count($filteredIds);

        $genreQuery = (clone $baseQuery)->whereNotNull('GENERE')->where('GENERE', '!=', '');

        if ($selectedYear) {
            $genreQuery->whereYear('data', $selectedYear);
        }

        $genreOptions = $genreQuery
            ->selectRaw('`GENERE`, COUNT(*) as count')
            ->groupBy('GENERE')
            ->orderByDesc('count')
            ->pluck('count', 'GENERE');

        // Main results query - keep MATCH only for relevance ordering
        $resultsQuery = Recording::whereIn('recordings.id', $filteredIds)
            ->with(['transcript', 'audio'])
            ->select('recordings.id', 'recordings.data', 'recordings.AUTORE', 'recordings.DESTINATARI', 'recordings.GENERE', 'recordings.code', 'recordings.argomento', 'recordings.LOCALITA');

        if ($selectedYear) {
            $resultsQuery->whereYear('data', $selectedYear);
        }

        if (! empty($q)) {
            $resultsQuery
                ->join('recording_transcripts', 'recording_transcripts.recording_id', '=', 'recordings.id')
                ->selectRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE) as relevance', [$q])
                ->orderByDesc('relevance');
        } else {
            $resultsQuery->orderBy('data');
        }

        $filteredCount = (clone $resultsQuery)->count();
        $transcripts = $resultsQuery->limit(200)->get();

        $maxCount = $countByDecade->max('count') ?: 1;

        $selectedDoc = $selectedDocId ? $transcripts->firstWhere('id', $selectedDocId) : null;

        return view('archive.index', compact('countByDecade', 'transcripts', 'filteredCount', 'genreOptions', 'maxCount', 'totalCount', 'selectedYear', 'selectedDocId', 'selectedGenere', 'selectedDoc'));
    }

    public function audio(int $id): StreamedResponse
    {
        $audio = RecordingAudio::findOrFail($id);
        $disk = Storage::disk('audio_originals');

        abort_unless($disk->exists($audio->file_path), 404);

        return $disk->response($audio->file_path, $audio->file_name, [
            'Content-Type' => 'audio/mpeg',
            'Accept-Ranges' => 'bytes',
        ]);
    }
}
