<?php

declare(strict_types=1);

namespace App\Archive\Controllers;

use App\Archive\Models\Recording;
use App\Archive\Models\RecordingAudio;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ArchiveController
{
    public function index()
    {
        $qInput = request('q', '');
        $selectedYearInput = request('year');
        $selectedGenereInput = request('genere');
        $selectedDocIdInput = request('doc');

        $q = is_string($qInput) ? $qInput : '';
        $selectedYear = is_numeric($selectedYearInput) ? (int) $selectedYearInput : null;
        $selectedGenere = is_string($selectedGenereInput) && $selectedGenereInput !== '' ? $selectedGenereInput : null;
        $selectedDocId = is_numeric($selectedDocIdInput) ? (int) $selectedDocIdInput : null;

        // Execute MATCH once to get filtered IDs
        /** @var EloquentBuilder<Recording> $matchQuery */
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
        /** @var EloquentBuilder<Recording> $baseQuery */
        $baseQuery = Recording::query()->whereIn('recordings.id', $filteredIds);

        $countByDecade = (clone $baseQuery)
            ->whereNotNull('data')
            ->selectRaw('YEAR(data) as decade, COUNT(*) as count')
            ->groupBy('decade')
            ->orderBy('decade')
            ->get();

        $totalCount = count($filteredIds);

        /** @var EloquentBuilder<Recording> $problemsQuery */
        $problemsQuery = Recording::query()->whereIn('recordings.id', $filteredIds);

        if ($selectedYear) {
            $problemsQuery->whereYear('data', $selectedYear);
        }

        $missingTranscriptCount = (clone $problemsQuery)->whereDoesntHave('transcript')->count();
        $missingMp3Count = (clone $problemsQuery)->whereDoesntHave('audio')->count();

        /** @var EloquentBuilder<Recording> $genreQuery */
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
        /** @var EloquentBuilder<Recording> $resultsQuery */
        $resultsQuery = Recording::query();
        $resultsQuery->whereIn('recordings.id', $filteredIds);
        $resultsQuery->with(['transcript', 'audio']);
        $resultsQuery->select('recordings.id', 'recordings.data', 'recordings.AUTORE', 'recordings.DESTINATARI', 'recordings.GENERE', 'recordings.code', 'recordings.argomento', 'recordings.LOCALITA');

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

        return view('archive.index', compact('countByDecade', 'transcripts', 'filteredCount', 'genreOptions', 'maxCount', 'totalCount', 'selectedYear', 'selectedDocId', 'selectedGenere', 'selectedDoc', 'missingTranscriptCount', 'missingMp3Count'));
    }

    public function audio(int $id): BinaryFileResponse
    {
        $audio = RecordingAudio::query()->findOrFail($id);
        $disk = Storage::disk('audio_originals');

        abort_unless($disk->exists($audio->file_path), 404);

        return response()->file($disk->path($audio->file_path), [
            'Content-Type' => 'audio/mpeg',
        ]);
    }
}
