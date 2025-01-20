<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Autore as Autore;
use App\Biblioteca\Models\Classificazione as Classificazione;
use App\Biblioteca\Models\Editore as Editore;
use App\Biblioteca\Models\Libro as Libro;
use App\Biblioteca\Models\ViewCollocazione as ViewCollocazione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class SearchableLibriController
{
    public function index()
    {
        $classificazioni = Classificazione::orderBy('descrizione', 'ASC')->get();
        $CollocazioneLettere = ViewCollocazione::lettere()->get();

        return view('biblioteca.libri.search.index', ['classificazioni' => $classificazioni,
            'lettere' => $CollocazioneLettere]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'xIdEditore' => 'exists:db_biblioteca.editore,id',
            'xIdAutore' => 'exists:db_biblioteca.autore,id',
            'xClassificazione' => 'exists:db_biblioteca.classificazione,id',
        ], [
            'xIdEditore.exists' => 'Editore inserito non esiste.',
            'xIdAutore.exists' => 'Autore inserito non esiste.',
            'xClassificazione.exists' => 'Classificazione inserita non esiste.',
        ]);

        $msgSearch = ' ';
        $orderBy = 'titolo';

        if (! $request->except(['_token'])) {
            return redirect()->route('books.index')->withError('Nessun criterio di ricerca selezionato oppure invalido');
        }

        if ($request->filled('xIdEditore')) {
            $editore = Editore::findOrFail($request->xIdEditore);
            $msgSearch = $msgSearch." Editore= $editore->editore;";
            $idEditore = $request->xIdEditore;
            $queryLibri = Editore::find($editore->id)->libri();
            $orderBy = 'collocazione';
        }
        $queryLibri = Libro::sortable()->where(function ($q) use ($request, &$msgSearch, &$orderBy): void {
            if ($request->xTitolo) {
                $titolo = $request->xTitolo;
                $q->where('titolo', 'like', "%$titolo%");
                $msgSearch = $msgSearch.'Titolo='.$titolo;
                $orderBy = 'titolo';
            }
            if ($request->xCollocazione) {
                $collocazione = $request->xCollocazione;
                if ($collocazione === 'null') {
                    $q->where('collocazione', '=', '')->orWhereNull('collocazione');
                    $msgSearch = $msgSearch.' Collocazione=SENZA collocazione';
                } else {
                    $q->where('collocazione', 'like', "%$collocazione%");
                    $msgSearch = $msgSearch.' Collocazione='.$collocazione;
                }
                $orderBy = 'collocazione';
            }
            if ($request->filled('xIdEditore')) {
                $editore = Editore::findOrFail($request->xIdEditore);
                $msgSearch = $msgSearch." Editore= $editore->editore;";
                $idEditore = $request->xIdEditore;
                $q->where('ID_EDITORE', $idEditore);
                $orderBy = 'collocazione';
            }
            if ($request->filled('xIdAutore')) {
                $idAutore = $request->xIdAutore;
                $q->where('ID_AUTORE', $idAutore);
                $autore = Autore::findOrFail($idAutore)->autore;
                $msgSearch = $msgSearch." Autore=$autore";
            }
            if ($request->filled('xClassificazione')) {
                $classificazione = (int) $request->xClassificazione;
                if ($classificazione === 0) { // NON CLASSIFICATO
                    $q->where('classificazione_id', "$classificazione")->orWhereNull('classificazione_id');
                } else {
                    $q->where('classificazione_id', "$classificazione");
                }
                $class = Classificazione::findOrFail($classificazione)->descrizione;
                $msgSearch = $msgSearch.' Classificazione='.$class;
            }
            if ($request->xNote) {
                $note = $request->xNote;
                $q->where('note', 'like', "%$note%");
                $msgSearch = $msgSearch.' Note='.$note;
            }
            if ($request->xCategoria) {
                $categoria = $request->xCategoria;
                $q->where('categoria', $categoria);
                $msgSearch = $msgSearch.' Categoria='.$categoria;
            }
        });

        // SQL query used to add the etichette to be printed (this query is sent to the etichetteController@Add)
        $query = str_replace(['?'], ['\'%s\''], $queryLibri->toSql());
        $query = vsprintf($query, $queryLibri->getBindings());

        // show also the libri delted only if the authneticated user has role bibioteca
        if (Auth::check() and Auth::user()->hasRole('biblioteca')) {
            $libri = $queryLibri->withTrashed()->orderBy($orderBy)->paginate(50);
        } else {
            $libri = $queryLibri->orderBy($orderBy)->paginate(50);
        }

        $classificazioni = Classificazione::orderBy('descrizione', 'ASC')->get();

        return view('biblioteca.libri.search_results', ['libri' => $libri,
            'classificazioni' => $classificazioni,
            'msgSearch' => $msgSearch,
            'query' => $query,
        ]);
    }
}
