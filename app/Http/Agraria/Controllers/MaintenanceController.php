<?php

declare(strict_types=1);

namespace App\Agraria\Controllers;

use App\Agraria\Models\Manutenzione;
use App\Agraria\Models\ManutenzioneProgrammata;
use App\Agraria\Models\MezzoAgricolo;
use Illuminate\Http\Request;

final class MaintenanceController
{
    public function create()
    {
        $mezzi = MezzoAgricolo::orderBy('nome', 'asc')->get();
        $programmate = ManutenzioneProgrammata::orderBy('ore', 'asc')->get();

        return view('agraria.maintenanance.create', compact('mezzi', 'programmate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mezzo' => 'required',
            'data' => 'required',
            'ore' => 'required',
            'persona' => 'required',
        ], [
            'mezzo.required' => 'Il mezzo è richiesto',
            'data.required' => 'La data è richiesta',
            'ore.required' => 'Le ore sono richieste',
            'persona.required' => 'La persona è richiesta',
        ]);

        if (! $request->filled('programmate') && ! $request->filled('straordinarie')) {
            return redirect()->back()->withErrors('Almeno una manutenzione programmata o straordinaria deve essere fornita');
        }

        $mezzo = MezzoAgricolo::find($request->input('mezzo'));

        $nuova_manutenzione = new Manutenzione;
        $nuova_manutenzione->data = $request->input('data');
        $nuova_manutenzione->ore = $request->input('ore');
        $nuova_manutenzione->spesa = $request->input('spesa', 0);
        $nuova_manutenzione->persona = $request->input('persona');
        $nuova_manutenzione->lavori_extra = $request->input('straordinarie', null);
        $nuova_manutenzione->mezzo_agricolo = $request->input('mezzo');
        $nuova_manutenzione->save();

        if ($request->filled('programmate')) {
            $nuova_manutenzione->programmate()->attach($request->get('programmate'));
        }

        return redirect()->route('agraria.index')->withSuccess("Manutenzione $mezzo->nome salvata correttamente");
    }
}
