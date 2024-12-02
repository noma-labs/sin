<?php

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Classificazione as Classificazione;
use Illuminate\Http\Request;

class ClassificazioniController
{
    public function index()
    {
        $classificazioni = Classificazione::orderBy('descrizione')->paginate(20); //Get all classificazioni

        return view('biblioteca.libri.classificazioni.index')->with('classificazioni', $classificazioni);
    }

    public function create()
    {
        return view('biblioteca.libri.classificazioni.create');
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

        return redirect()->route('classificazioni.index')->withSuccess('Classificazione '.$classificazione->descrizione.' aggiunto!');

    }

    public function show($id)
    {
        return redirect('classificazioni');
    }

    public function edit($id)
    {
        $classificazione = Classificazione::findOrFail($id);

        return view('biblioteca.libri.classificazioni.edit')->with('classificazione', $classificazione);
    }

    public function searchClassificazione(Request $request)
    {
        $term = $request->term;
        if ($term) {
            $classificazioni = Classificazione::where('descrizione', 'LIKE', '%'.$term.'%')->orderBy('descrizione')->get();
        }
        if (! empty($classificazioni)) {
            $results = [];
            foreach ($classificazioni as $classificazione) {
                $results[] = ['value' => $classificazione->id, 'label' => $classificazione->descrizione, 'url' => route('classificazioni.edit', [$classificazione->id])];
            }

            return response()->json($results);
        } else {
            return response()->json(['value' => '', 'label' => 'classificazione inesistente']);
        }

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
        $classificazione = Classificazione::findOrFail($id); //Get role with the given id
        $vecchiaDescrizionee = $classificazione->descrizione;
        $classificazione->fill($request->only('descrizione'));
        if ($classificazione->save()) {
            return redirect()->route('classificazioni.index')->withSuccess("Classificazione  $vecchiaDescrizionee aggiornato in '. $classificazione->descrizione.' aggiornato in ");
        }

        return redirect()->route('classificazioni.index')->withError("Errore durante l'operaizone di aggiornamento");
    }

    public function destroy($id)
    {
        return redirect()->route('classificazioni.index')->withError("Impossibile eliminare la classificazione");
    }
}
