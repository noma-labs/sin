<?php

declare(strict_types=1);

namespace App\Archive\Controllers;

use App\Archive\Models\ArchivioDocumento;
use App\Archive\Models\AudioTranscript;
use Illuminate\Http\Request;

final class SearchableArchiveController
{

    public function search()
    {
        $term = request()->query('q', '');
        $results = [];

        if (! empty($term)) {
            $results = AudioTranscript::selectRaw(
                '*, MATCH(content) AGAINST(? IN BOOLEAN MODE) as relevance',
                [$term]
            )->whereRaw(
                'MATCH(content) AGAINST(? IN BOOLEAN MODE)',
                [$term]
            )->orderByRaw(
                'MATCH(content) AGAINST(? IN BOOLEAN MODE) DESC',
                [$term]
            )->get();
        }

        return view('archive.search', ['results' => $results, 'term' => $term]);
    }

}
