<?php

declare(strict_types=1);

namespace App\Archive\Controllers;

use App\Archive\Models\ArchivioDocumento;
use App\Archive\Models\RecordingTranscript;
use App\Photo\Models\Photo;
use Illuminate\Http\Request;

final class ArchiveController
{
    public function index()
    {
        $countByDecade = RecordingTranscript::selectRaw('YEAR(recorded_date) as decade, COUNT(*) as count')
            ->whereNotNull('recorded_date')
            ->groupBy('decade')
            ->orderBy('decade')
            ->get();
        $totalCount = RecordingTranscript::count();
        $photosCount = Photo::count();

        $selectedYear = request('year');
        $selectedDocId = request('doc');
        $selectedDocWords = collect();
        $countByMonth = collect();
        $transcripts = collect();
        if ($selectedYear) {
            $query = RecordingTranscript::whereYear('recorded_date', $selectedYear)->orderBy('recorded_date');
            $transcripts = $query->get(['id', 'code', 'title', 'description', 'recorded_date', 'content']);
            // Get count by month
            $countByMonth = RecordingTranscript::selectRaw('MONTH(recorded_date) as month, COUNT(*) as count')
                ->whereYear('recorded_date', $selectedYear)
                ->whereNotNull('recorded_date')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month');
            // Collect all words from all titles in the selected year
            $stopwords = [
                'del', 'della', 'delle', 'dello', 'dell', 'dei', 'degli', 'dai', 'dagli', 'dal', 'dalla', 'dalle', 'nella',
                'con', 'per', 'tra', 'fra', 'sul', 'sulla', 'sulle', 'sugli', 'sui', 'sul', 'su',
                'che', 'non', 'una', 'uno', 'un', 'le', 'la', 'il', 'lo', 'gli', 'i', 'in', 'di', 'a', 'e', 'o', 'ed', 'al', 'ai', 'agli', 'nel', 'all', 'alla', 'alle', 'ma', 'se', 'da', 'ha', 'ho', 'hanno', 'sono', 'era', 'erano', 'come', 'più', 'meno', 'anche', 'questo', 'questa', 'questi', 'queste', 'quello', 'quella', 'quelli', 'quelle', 'ci', 'vi', 'ne', 'mi', 'ti', 'si', 'vi', 'loro', 'noi', 'voi', 'tu', 'io', 'lui', 'lei', 'mio', 'tuo', 'suo', 'nostro', 'vostro', 'loro', 'il', 'la', 'i', 'gli', 'le', 'un', 'una', 'uno', 'di', 'a', 'da', 'in', 'con', 'su', 'per', 'tra', 'fra', 'al', 'allo', 'ai', 'agli', 'alla', 'alle', 'del', 'dello', 'dei', 'degli', 'della', 'delle', 'sul', 'sullo', 'sui', 'sugli', 'sulla', 'sulle', 'e', 'o', 'ma', 'anche', 'come', 'dove', 'quando', 'che', 'chi', 'cui', 'quale', 'quali', 'quanto', 'quanta', 'quanti', 'quante', 'il', 'lo', 'la', 'i', 'gli', 'le', 'un', 'uno', 'una', 'di', 'a', 'da', 'in', 'con', 'su', 'per', 'tra', 'fra', 'al', 'allo', 'ai', 'agli', 'alla', 'alle', 'del', 'dello', 'dei', 'degli', 'della', 'delle', 'sul', 'sullo', 'sui', 'sugli', 'sulla', 'sulle', 'e', 'o', 'ma', 'anche', 'come', 'dove', 'quando', 'che', 'chi', 'cui', 'quale', 'quali', 'quanto', 'quanta', 'quanti', 'quante',
            ];
            $allWords = $transcripts->flatMap(fn ($doc) => preg_split('/\W+/', mb_strtolower($doc->title ?? '')));
            $selectedDocWords = collect($allWords)
                ->filter(fn ($w) => mb_strlen((string) $w) > 2 && ! in_array($w, $stopwords))
                ->countBy()
                ->sortDesc()
                ->take(20);
        }

        $maxCount = $countByDecade->max('count') ?: 1;

        $selectedMonth = request('month');
        $selectedDoc = $selectedDocId ? $transcripts->firstWhere('id', $selectedDocId) : null;
        $months = [
            1 => 'Gennaio',
            2 => 'Febbraio',
            3 => 'Marzo',
            4 => 'Aprile',
            5 => 'Maggio',
            6 => 'Giugno',
            7 => 'Luglio',
            8 => 'Agosto',
            9 => 'Settembre',
            10 => 'Ottobre',
            11 => 'Novembre',
            12 => 'Dicembre',
        ];

        return view('archive.index', compact('countByDecade', 'transcripts', 'selectedDocWords', 'countByMonth', 'maxCount', 'totalCount', 'photosCount', 'selectedYear', 'selectedDocId', 'selectedMonth', 'selectedDoc', 'months'));
    }

    public function show($id)
    {
        $transcript = RecordingTranscript::findOrFail($id);
        return view('archive.show', ['transcript' => $transcript]);
    }
}
