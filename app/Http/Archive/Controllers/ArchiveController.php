<?php

declare(strict_types=1);

namespace App\Archive\Controllers;

use App\Archive\Models\Recording;

final class ArchiveController
{
    public function index()
    {
        $countByDecade = Recording::selectRaw('YEAR(data) as decade, COUNT(*) as count')
            ->whereNotNull('data')
            ->groupBy('decade')
            ->orderBy('decade')
            ->get();
        $totalCount = Recording::count();

        $selectedYear = request('year');
        $selectedMonth = request('month');
        $selectedDocId = request('doc');
        $selectedGenere = request('genere');
        $selectedDocWords = collect();
        $countByMonth = collect();
        $genreOptions = collect();

        $query = Recording::with('transcript')->orderBy('data');

        if ($selectedYear) {
            $query->whereYear('data', $selectedYear);
        }

        if ($selectedMonth) {
            $query->whereMonth('data', $selectedMonth);
        }

        if ($selectedGenere) {
            $query->where('GENERE', $selectedGenere);
        }

        $filteredCount = (clone $query)->count();
        $transcripts = $query->limit(200)->get(['id', 'data', 'AUTORE', 'DESTINATARI', 'GENERE', 'code', 'argomento']);

        $genreQuery = Recording::whereNotNull('GENERE')->where('GENERE', '!=', '');
        $monthQuery = Recording::whereNotNull('data');

        if ($selectedYear) {
            $genreQuery->whereYear('data', $selectedYear);
            $monthQuery->whereYear('data', $selectedYear);
        }

        if ($selectedMonth) {
            $genreQuery->whereMonth('data', $selectedMonth);
        }

        $genreOptions = $genreQuery
            ->selectRaw('`GENERE`, COUNT(*) as count')
            ->groupBy('GENERE')
            ->orderByDesc('count')
            ->pluck('count', 'GENERE');

        $countByMonth = $monthQuery
            ->selectRaw('MONTH(data) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // Collect all words from all titles
        $stopwords = [
            'del', 'della', 'delle', 'dello', 'dell', 'dei', 'degli', 'dai', 'dagli', 'dal', 'dalla', 'dalle', 'nella',
            'con', 'per', 'tra', 'fra', 'sul', 'sulla', 'sulle', 'sugli', 'sui', 'sul', 'su',
            'che', 'non', 'una', 'uno', 'un', 'le', 'la', 'il', 'lo', 'gli', 'i', 'in', 'di', 'a', 'e', 'o', 'ed', 'al', 'ai', 'agli', 'nel', 'all', 'alla', 'alle', 'ma', 'se', 'da', 'ha', 'ho', 'hanno', 'sono', 'era', 'erano', 'come', 'più', 'meno', 'anche', 'questo', 'questa', 'questi', 'queste', 'quello', 'quella', 'quelli', 'quelle', 'ci', 'vi', 'ne', 'mi', 'ti', 'si', 'vi', 'loro', 'noi', 'voi', 'tu', 'io', 'lui', 'lei', 'mio', 'tuo', 'suo', 'nostro', 'vostro', 'loro', 'il', 'la', 'i', 'gli', 'le', 'un', 'una', 'uno', 'di', 'a', 'da', 'in', 'con', 'su', 'per', 'tra', 'fra', 'al', 'allo', 'ai', 'agli', 'alla', 'alle', 'del', 'dello', 'dei', 'degli', 'della', 'delle', 'sul', 'sullo', 'sui', 'sugli', 'sulla', 'sulle', 'e', 'o', 'ma', 'anche', 'come', 'dove', 'quando', 'che', 'chi', 'cui', 'quale', 'quali', 'quanto', 'quanta', 'quanti', 'quante', 'il', 'lo', 'la', 'i', 'gli', 'le', 'un', 'uno', 'una', 'di', 'a', 'da', 'in', 'con', 'su', 'per', 'tra', 'fra', 'al', 'allo', 'ai', 'agli', 'alla', 'alle', 'del', 'dello', 'dei', 'degli', 'della', 'delle', 'sul', 'sullo', 'sui', 'sugli', 'sulla', 'sulle', 'e', 'o', 'ma', 'anche', 'come', 'dove', 'quando', 'che', 'chi', 'cui', 'quale', 'quali', 'quanto', 'quanta', 'quanti', 'quante',
        ];
        $allWords = $transcripts->flatMap(fn ($doc) => preg_split('/\W+/', mb_strtolower($doc->argomento ?? '')));
        $selectedDocWords = collect($allWords)
            ->filter(fn ($w) => mb_strlen((string) $w) > 2 && ! in_array($w, $stopwords))
            ->countBy()
            ->sortDesc()
            ->take(20);

        $maxCount = $countByDecade->max('count') ?: 1;

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

        return view('archive.index', compact('countByDecade', 'transcripts', 'filteredCount', 'selectedDocWords', 'countByMonth', 'genreOptions', 'maxCount', 'totalCount', 'selectedYear', 'selectedDocId', 'selectedMonth', 'selectedGenere', 'selectedDoc', 'months'));
    }

    public function show($id)
    {
        $transcript = Recording::with('transcript')->findOrFail($id);
        dd($transcript);

        return view('archive.show', ['transcript' => $transcript]);
    }
}
