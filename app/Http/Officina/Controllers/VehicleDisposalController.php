<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\Marche as Marca;
use App\Officina\Models\Modelli as Modello;
use App\Officina\Models\Veicolo;
use Illuminate\Http\Request;

final class VehicleDisposalController
{
    public function index(Request $request)
    {
        $marche = Marca::orderBy('nome', 'asc')->get();
        $modelli = Modello::orderBy('nome', 'asc')->get();

        $veicoli = Veicolo::onlyTrashed()->orderBy('veicolo.nome', 'asc');
        if ($request->filled('marca')) {
            $veicoli->join('db_meccanica.modello', 'veicolo.modello_id', '=', 'modello.id')
                ->where('modello.marca_id', '=', $request->input('marca'));
        }
        if ($request->filled('nome')) {
            $veicoli->where('veicolo.nome', 'like', $request->input('nome').'%');
        }
        if ($request->filled('targa')) {
            $veicoli->where('veicolo.targa', 'like', '%'.$request->input('targa').'%');
        }
        if ($request->filled('modello')) {
            $veicoli->where('veicolo.modello_id', '=', $request->input('modello'));
        }
        $veicoli = $veicoli->get();

        return view('officina.veicoli.show-demoliti', compact('veicoli', 'marche', 'modelli'));
    }

    public function destroy($id)
    {
        $veicolo = Veicolo::onlyTrashed()->find($id);
        $veicolo->forceDelete();

        return redirect(route('veicoli.demoliti'))->withSuccess("Il veicolo $veicolo->nome è stato eliminato definitivamente");
    }

    public function update($id)
    {
        $veicolo = Veicolo::onlyTrashed()->find($id);
        $veicolo->restore();

        return redirect(route('veicoli.index'))->withSuccess("Il veicolo $veicolo->nome è stato riabilitato");
    }
}
