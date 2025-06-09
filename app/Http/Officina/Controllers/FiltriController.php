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

    //     public function aggiungiFiltro(Request $request)
    // {
    //     $request->validate([
    //         'codice' => 'required',
    //         'tipo' => 'required',
    //     ]);

    //     $note = $request->input('note', '');

    //     // salva il filtro
    //     try {
    //         $filtro = TipoFiltro::create([
    //             'codice' => mb_strtoupper((string) $request->input('codice')),
    //             'tipo' => $request->input('tipo'),
    //             'note' => $note,
    //         ]);
    //     } catch (Throwable) {
    //         return redirect(route('veicoli.modifica', ['id' => $request->input('veicolo')]))->withError('Errore durante il salvataggio del filtro: filtro già esistente');
    //     }

    //     if ($filtro) {
    //         return redirect(route('veicoli.modifica', ['id' => $request->input('veicolo')]))->withSuccess("Filtro $filtro->codice salvato correttamente");
    //     }

    //     return redirect(route('veicoli.modifica', ['id' => $request->input('veicolo')]))->withError("Errore durante il salvataggio del filtro $filtro->codice");

    // }

    public function delete($id)
    {
        $filtro = TipoFiltro::findOrFail($id);
        $filtro->delete();

        return redirect()->back()->withSuccess("Filtro $filtro->codice eliminato con successo.");
    }
}
