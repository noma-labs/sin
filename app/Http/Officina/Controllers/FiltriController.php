<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\TipoFiltro;
use Illuminate\Http\Request;

final class FiltriController
{
    public function index()
    {
        $filtri = TipoFiltro::all()->sortBy('tipo');

        return view('officina.filters.index', compact('filtri'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codice' => 'required',
        ]);
        $gomma = TipoFiltro::create([
            'codice' => $request->input('codice'),
        ]);

        return redirect()->back()->withSuccess("Filtro $gomma->codice salvato correttamente");
    }

    public function delete($id)
    {
        $filtro = TipoFiltro::findOrFail($id);
        $filtro->delete();

        return redirect()->back()->withSuccess("Filtro $filtro->codice eliminato con successo.");
    }
}
