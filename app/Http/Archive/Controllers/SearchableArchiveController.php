<?php

declare(strict_types=1);

namespace App\Archive\Controllers;

use App\Archive\Models\RecordingTranscript;

final class SearchableArchiveController
{
    public function search()
    {
        $term = request()->query('q', '');
        $results = [];

        if (! empty($term)) {
            $results = RecordingTranscript::query()
                ->select('recording_transcripts.*')
                ->selectRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE) as relevance', [$term])
                ->whereRaw('MATCH(recording_transcripts.content) AGAINST(? IN BOOLEAN MODE)', [$term])
                ->with([
                    'recording:id,code,DATA,ORE,AUTORE,ARGOMENTO,LOCALITA',
                ])
                ->orderByDesc('relevance')
                ->get();
        }

        return view('archive.search', ['results' => $results, 'term' => $term]);
    }
}
