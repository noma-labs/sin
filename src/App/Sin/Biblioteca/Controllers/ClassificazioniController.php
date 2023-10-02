<?php

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Classificazione as Classificazione;
use App\Core\Controllers\BaseController as CoreBaseController;
use Illuminate\Http\Request;

class ClassificazioniController extends CoreBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classificazioni = Classificazione::orderBy('descrizione')->paginate(20); //Get all classificazioni

        return view('biblioteca.libri.classificazioni.index')->with('classificazioni', $classificazioni);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('biblioteca.libri.classificazioni.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'descrizione' => 'required|unique:db_biblioteca.classificazione,descrizione',
        ], [
                'descrizione.required' => 'La classificazione non può essere vuoto.',
                'descrizione.unique' => "La classificazione $request->descrizione esistente già.",
            ]
        );
        // $classificazione = new Classificazione;
        // $classificazione->descrizione = $request->classificazione;
        // $classificazione->save();
        $classificazione = Classificazione::create($request->only('descrizione'));

        return redirect()->route('classificazioni.index')->withSuccess('Classificazione ' . $classificazione->descrizione . ' aggiunto!');

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('classificazioni');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $classificazione = Classificazione::findOrFail($id);

        return view('biblioteca.libri.classificazioni.edit')->with('classificazione', $classificazione);
    }

    public function searchClassificazione(Request $request)
    {
        $term = $request->term;
        if ($term) {
            $classificazioni = Classificazione::where('descrizione', 'LIKE', '%' . $term . '%')->orderBy('descrizione')->get();
        }
        if (!empty($classificazioni)) {
            foreach ($classificazioni as $classificazione) {
                $results[] = ['value' => $classificazione->id, 'label' => $classificazione->descrizione, 'url' => route('classificazioni.edit', [$classificazione->id])];
            }

            return response()->json($results);
        } else {
            return response()->json(['value' => '', 'label' => 'classificazione inesistente']);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $id;
        $this->validate($request, [
            'descrizione' => 'required|unique:db_biblioteca.classificazione,descrizione,' . $id . ',id',
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
        return redirect()->route('classificazioni.index')->withErroe("Errore durante l'operaizone di aggiornamento");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->route('classificazioni.index')->withError("Impossibile eliminare l'autore");
    }
}
