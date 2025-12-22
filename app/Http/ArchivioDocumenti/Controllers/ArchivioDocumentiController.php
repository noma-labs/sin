<?php

declare(strict_types=1);

namespace App\ArchivioDocumenti\Controllers;

use App\ArchivioDocumenti\Models\ArchivioDocumento;
use Illuminate\Http\Request;

final class ArchivioDocumentiController
{
    public function index()
    {
        $libri = ArchivioDocumento::orderby('foglio')->paginate(100);

        return view('archiviodocumenti.libri.search', compact('libri'));
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
