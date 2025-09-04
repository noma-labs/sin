<?php

declare(strict_types=1);

namespace App\Agraria\Controllers;

use App\Agraria\Models\Manutenzione;
use App\Agraria\Models\ManutenzioneProgrammata;
use App\Agraria\Models\ManutenzioneTipo;
use App\Agraria\Models\MezzoAgricolo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class MaintenanceController
{
    public function create(): View
    {
        $mezzi = MezzoAgricolo::query()->orderBy('nome', 'asc')->get();
        $programmate = ManutenzioneProgrammata::query()->orderBy('ore', 'asc')->get();

        return view('agraria.maintenanance.create', compact('mezzi', 'programmate'));
    }

    public function store(Request $request): RedirectResponse
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

        return redirect()->route('agraria.index')->with('success', "Manutenzione $mezzo->nome salvata correttamente");
    }

    public function show(int $id): View
    {
        $manutenzione = Manutenzione::with('mezzo', 'programmate')->findOrFail($id);

        return view('agraria.maintenanance.show', compact('manutenzione'));
    }

    public function edit(int $id): View
    {
        $manutenzione = Manutenzione::with('mezzo', 'programmate')->findOrFail($id);
        $programmate = ManutenzioneProgrammata::orderBy('ore', 'asc')->get();

        return view('agraria.maintenanance.edit', compact('manutenzione', 'programmate'));
    }

    public function update(Request $request, int $id): RedirectResponse
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

        $manutenzione = Manutenzione::query()->findOrFail($id);
        $manutenzione->data = $request->input('data');
        $manutenzione->ore = $request->input('ore');
        $manutenzione->spesa = $request->input('spesa', 0);
        $manutenzione->persona = $request->input('persona');
        $manutenzione->lavori_extra = $request->input('straordinarie', null);
        $manutenzione->mezzo_agricolo = $request->input('mezzo');
        $manutenzione->save();

        // Sync manutenzioni programmate
        $manutenzione->programmate()->sync($request->get('programmate', []));

        return redirect()->route('agraria.maintenanace.show', $manutenzione->id)
            ->with('success', 'Manutenzione aggiornata correttamente');
    }

    public function destroy(int $id): RedirectResponse
    {
        $manutenzione = Manutenzione::with('programmate')->findOrFail($id);
        ManutenzioneTipo::where('manutenzione', $manutenzione->id)->delete();
        $manutenzione->delete();

        return redirect()->route('agraria.index')->with('success', 'Manutenzione eliminata correttamente');
    }
}
