<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Classificazione as Classificazione;
use App\Biblioteca\Models\Libro as Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class BooksController
{
    public function create()
    {
        $classificazioni = Classificazione::orderBy('descrizione')->get();

        return view('biblioteca.libri.create', compact('classificazioni'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'xTitolo' => 'required',
            'xIdAutori' => 'required',
            'xIdEditori' => 'required',
            'xCollocazione' => 'required|unique:db_biblioteca.libro,collocazione',
            'xClassificazione' => 'required|exists:db_biblioteca.classificazione,id',
        ], [
            'xTitolo.required' => 'Il titolo del libro è obbligatorio.',
            'xIdAutori.required' => "L'Autore del libro è obbligatorio.",
            'xIdEditori.required' => "L'Editore del libro è obbligatorio.",
            'xClassificazione.exists' => 'La classificazione inserita non è valida.',
            'xClassificazione.required' => 'La classificazione è obbligatoria.',
            'xCollocazione.unique' => 'La collocazione inserita esiste già.',
            'xCollocazione.required' => 'La collocazione inserita non corretta.',
        ]);

        $_addanother = $request->input('_addanother');  // save and add another libro

        if ($request->xCollocazione === 'null') {
            $collocazione = null;
        } else {
            $collocazione = $request->xCollocazione;
        }

        $libro = new Libro;
        $libro->titolo = $request->xTitolo;
        $libro->collocazione = $collocazione;
        $libro->classificazione_id = $request->xClassificazione;
        $libro->note = $request->xNote;
        $libro->fill($request->only('isbn', 'data_pubblicazione', 'categoria', 'dimensione', 'critica'));

        $etichetta_criterio = $request->input('stampaEtichetta'); //radio buttons for printing or no the etichetta
        $msg_etichetta = '';
        switch ($etichetta_criterio) {
            case 'aggiungiEtichetta':
                $libro->tobe_printed = 1;
                $msg_etichetta = " L'etichetta del libro aggiunta alla lista di etichette da stampare.";
                break;
            case 'noEtichetta':
                $libro->tobe_printed = 0;
                $msg_etichetta = "L'etichetta non viene stampata";
                break;
        }
        DB::transaction(function () use ($libro, $request): void {
            $libro->save();
            $libro->autori()->sync($request->xIdAutori);
            $libro->editori()->sync($request->xIdEditori);
        });
        if ($_addanother) {
            return redirect()->route('books.create')->withSuccess('Libro inserito correttamente.'.$msg_etichetta);
        }

        return redirect()->route('books.show', [$libro->id])->withSuccess('Libro inserito correttamente.'.$msg_etichetta);
    }

    public function show($idLibro)
    {
        $libro = Libro::withTrashed()->find($idLibro);
        $prestitiAttivi = $libro->prestiti->where('in_prestito', 1);
        if ($libro) {
            return view('biblioteca.libri.show', ['libro' => $libro, 'prestitiAttivi' => $prestitiAttivi]);
        }

        return redirect()->route('books.index')->withError('Il libro selezionato non esiste');

    }

    public function edit($id)
    {
        $classificazioni = Classificazione::orderBy('descrizione', 'ASC')->get();
        $libro = Libro::findOrFail($id);

        return view('biblioteca.libri.edit', ['libro' => $libro, 'classificazioni' => $classificazioni]);

    }

    public function update(Request $request, $idLibro)
    {
        $request->validate([
            'xTitolo' => 'required',
            'xClassificazione' => 'required|exists:db_biblioteca.classificazione,id',
        ], [
            'xTitolo.required' => 'Il titolo del libro è obbligatorio.',
            'xClassificazione.exists' => 'La classificazione inserita non è valida.',
            'xClassificazione.required' => 'La classificazione è obbligatoria',
            'xCollocazione.unique' => 'La collocazione inserita esiste già.',
        ]);

        $libro = Libro::findOrFail($idLibro);
        $libro->titolo = $request->xTitolo;
        $libro->classificazione_id = $request->xClassificazione;
        $libro->note = $request->xNote;
        $libro->fill($request->only('isbn', 'data_pubblicazione', 'categoria', 'dimensione', 'critica'));

        DB::transaction(function () use ($libro, $request): void {
            $libro->save();
            $libro->autori()->sync($request->xIdAutori);
            $libro->editori()->sync($request->xIdEditori);
        });

        return redirect()->route('books.show', ['id' => $idLibro])->withSuccess('Libro modificato correttamente');
    }
}
