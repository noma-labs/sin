<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\TipoGomme;
use App\Officina\Models\Veicolo;
use Illuminate\Http\Request;

final class VeicoliGommeController
{
    public function delete($id, $idGomma)
    {
        $veicolo = Veicolo::find($id);
        $gomma = TipoGomme::findOrFail($idGomma);
        $veicolo->gomme()->detach($gomma->id);

        return redirect()->back()->withSuccess("Gomma $gomma->codice eliminata con successo.");
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'gomma_id' => 'required',
        ], [
            'gomma_id.required' => 'La gomma è obbligatoria.',
        ]);

        $veicolo = Veicolo::find($id);
        $gomma = TipoGomme::findOrFail($request->input('gomma_id'));
        $veicolo->gomme()->attach($gomma->id);

        return redirect()->back()->withSuccess("Gomma $gomma->codice aggiunta con successo.");
    }
}
