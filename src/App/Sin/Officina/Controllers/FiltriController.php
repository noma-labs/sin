<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\Alimentazioni;
use App\Officina\Models\Impiego;
use App\Officina\Models\Marche as Marca;
use App\Officina\Models\Modelli as Modello;
use App\Officina\Models\TipoFiltro;
use App\Officina\Models\TipoGomme;
use App\Officina\Models\Tipologia;
use App\Officina\Models\TipoOlio;
use App\Officina\Models\Veicolo;
use Illuminate\Http\Request;
use Throwable;

final class FiltriController
{

    public function index(Request $request)
    {
        $filtri = TipoFiltro::all()->sortBy('tipo');
        return view('officina.gestione.filtri', compact('filtri'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codice' => 'required',
        ]);
        $gomma = TipoFiltro::create([
            'codice' => $request->input('codice'),
        ]);

        return redirect()->back()->withSuccess("Filtro $gomma->codice salvata correttamente");
    }

    public function delete($id)
    {
        $filtro = TipoFiltro::findOrFail($id);
        $filtro->delete();

        return redirect()->back()->withSuccess("Filtro $filtro->codice eliminato con successo.");
    }
}
