<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Classificazione;
use Illuminate\Http\Request;

final class ClassificazioniController
{
    public function index()
    {
        $classificazioni = Classificazione::orderBy('descrizione')->paginate(20);

        return view('biblioteca.books.audience.index')->with('classificazioni', $classificazioni);
    }

    public function create()
    {
        return view('biblioteca.books.audience.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'descrizione' => 'required|unique:db_biblioteca.classificazione,descrizione',
        ], [
            'descrizione.required' => 'La classificazione non può essere vuoto.',
            'descrizione.unique' => "La classificazione $request->descrizione esistente già.",
        ]
        );
        $classificazione = Classificazione::create($request->only('descrizione'));

        return redirect()->route('audience.index')->withSuccess('Classificazione '.$classificazione->descrizione.' aggiunto!');
    }

    public function edit($id)
    {
        $classificazione = Classificazione::findOrFail($id);

        return view('biblioteca.books.audience.edit')->with('classificazione', $classificazione);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'descrizione' => 'required|unique:db_biblioteca.classificazione,descrizione,'.$id.',id',
        ], [
            'descrizione.required' => 'La classificazione non può essere vuoto.',
            'descrizione.unique' => "La classificazione $request->descrizione esistente già.",
        ]
        );
        $classificazione = Classificazione::findOrFail($id);
        $vecchiaDescrizionee = $classificazione->descrizione;
        $classificazione->fill($request->only('descrizione'));
        if ($classificazione->save()) {
            return redirect()->route('audience.index')->withSuccess("Classificazione  $vecchiaDescrizionee aggiornato in {$classificazione->descrizione}");
        }

        return redirect()->route('audience.index')->withError("Errore durante l'operaizone di aggiornamento");
    }

    public function destroy()
    {
        return redirect()->route('audience.index')->withError('Impossibile eliminare la classificazione');
    }
}
