<?php

declare(strict_types=1);

namespace App\ArchivioDocumenti\Controllers;

use App\ArchivioDocumenti\Models\ArchivioDocumento;
use App\ArchivioDocumenti\Models\AudioTranscript;
use Illuminate\Http\Request;

final class ArchivioDocumentiController
{
    public function index()
    {
        $countByDecade = AudioTranscript::selectRaw('YEAR(recorded_date) as decade, COUNT(*) as count')
            ->whereNotNull('recorded_date')
            ->groupBy('decade')
            ->orderBy('decade')
            ->get();

        $selectedYear = request('year');
        $selectedDocId = request('doc');
        $selectedDocWords = collect();
        $countByMonth = collect();
        $transcripts = collect();
        if ($selectedYear) {
            $query = AudioTranscript::whereYear('recorded_date', $selectedYear)->orderBy('recorded_date');
            $transcripts = $query->get(['id', 'code', 'title', 'description', 'recorded_date', 'content']);
            // Get count by month
            $countByMonth = AudioTranscript::selectRaw('MONTH(recorded_date) as month, COUNT(*) as count')
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

        return view('archiviodocumenti.index', compact('countByDecade', 'transcripts', 'selectedDocWords', 'countByMonth', 'maxCount', 'selectedYear', 'selectedDocId', 'selectedMonth', 'selectedDoc', 'months'));
    }

    public function elimina()
    {
        $res = ArchivioDocumento::toBePrinted()->update(['stato' => 0]);
        if ($res) {
            return to_route('archiviodocumenti.etichette')->withSuccess("Tutte le $res etichette sono state eliminate.");
        }

        return to_route('archiviodocumenti.etichette')->withError("Errore nell'operazione");

    }

    public function eliminaSingolo($id)
    {
        $libro = ArchivioDocumento::find($id);
        $res = $libro->update(['stato' => 0]);
        if ($res) {
            return to_route('archiviodocumenti.etichette')->withSuccess("Libro $libro->foglio, $libro->titolo eliminato dalla stampa delle etichette");
        }

        return to_route('archiviodocumenti.etichette')->withError("Errore nell'operazione");

    }

    public function etichette()
    {
        $libriTobePrinted = ArchivioDocumento::TobePrinted()->get();

        return view('archiviodocumenti.etichette.view', compact('libriTobePrinted'));
    }

    public function esporta(): void
    {
        // TODO use browsershot to generate pdf
    }

    public function aggiungi(Request $request)
    {
        $from = $request->input('fromCollocazione');
        $to = $request->input('toCollocazione', $request->input('fromCollocazione'));
        if ($request->input('action') === 'add') {
            $count = ArchivioDocumento::whereBetween('foglio', [$from, $to])->update(['stato' => 1]);

            return to_route('archiviodocumenti.etichette')->withSuccess("$count etichette aggiunte alla stampa");
        }
        $count = ArchivioDocumento::whereBetween('foglio', [$from, $to])->update(['stato' => 0]);

        return to_route('archiviodocumenti.etichette')->withSuccess("$count etichette rimosse dalla stampa");

    }

    public function ricerca(Request $request)
    {
        // $validatedData = $request->validate([
        //   'collocazione'=>"exists:db_biblioteca.editore,id",
        //   'titolo'=>"exists:db_biblioteca.autore,id",
        //   'autore'=>"exists:db_biblioteca.classificazione,id",
        //   'editore'=>"exists:db_biblioteca.classificazione,id",

        //   ],[
        //     'xIdEditore.exists' => 'Editore inserito non esiste.',
        //     'xIdAutore.exists' => 'Autore inserito non esiste.',
        //     'xClassificazione.exists' => 'Classificazione inserita non esiste.',
        // ]);

        $msgSearch = ' ';
        $orderBy = 'titolo';

        if (! $request->except(['_token'])) {
            return to_route('archiviodocumenti')->withError('Nessun criterio di ricerca selezionato oppure invalido');
        }

        $queryLibri = ArchivioDocumento::sortable()->where(function ($q) use ($request, &$msgSearch, &$orderBy): void {
            if ($request->titolo) {
                $titolo = $request->titolo;
                $q->where('titolo', 'like', "%$titolo%");
                $msgSearch = $msgSearch.'Titolo='.$titolo;
                $orderBy = 'titolo';
            }
            if ($request->collocazione) {
                $collocazione = $request->collocazione;
                if ($collocazione === 'null') {
                    $q->where('foglio', '=', '')->orWhereNull('collocazione');
                    $msgSearch = $msgSearch.' Collocazione=SENZA collocazione';
                } else {
                    $q->where('foglio', 'like', "%$collocazione%");
                    $msgSearch = $msgSearch.' Collocazione='.$collocazione;
                }
                $orderBy = 'foglio';
            }
            if ($request->filled('editore')) {
                $ed = $request->editore;
                $msgSearch = $msgSearch." Editore= $ed";
                $q->where('editore', 'like', "%$ed%");
                $orderBy = 'editore';
            }
            if ($request->filled('autore')) {
                $au = $request->autore;
                $msgSearch = $msgSearch." Autore= $au";
                $q->where('autore', 'like', "%$au%");
                $orderBy = 'autore';
            }
        });

        $libri = $queryLibri->orderBy($orderBy)->paginate(50);

        return view('archiviodocumenti.libri.search', compact('libri', 'msgSearch'));
    }
}
