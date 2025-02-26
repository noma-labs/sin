<?php

declare(strict_types=1);

namespace App\Agraria\Controllers;

use App\Agraria\Models\Manutenzione;
use App\Agraria\Models\ManutenzioneProgrammata;
use App\Agraria\Models\MezzoAgricolo;
use App\Agraria\Models\StoricoOre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

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
        $rules = [
            'mezzo' => 'required',
            'data' => 'required',
            'ore' => 'required',
            'persona' => 'required',
        ];
        if ($request->filled('mezzo')) {
            $mezzo = MezzoAgricolo::find($request->input('mezzo'));
            // $rules['ore'] = 'required|gte:'.$mezzo->tot_ore;
        }
        if (! $request->filled('programmate') && ! $request->filled('straordinarie')) {
            return redirect()->back()->withErrors('Almeno una manutenzione programmata o straordinaria deve essere fornita');
        }

        $msg = [
            'mezzo.required' => 'Il mezzo è richiesto',
            'data.required' => 'La data è richiesta',
            'ore.required' => 'Le ore sono richieste',
            // 'ore.gte' => 'Le ore devono essere più di '.$mezzo->tot_ore,
            'persona.required' => 'La persona è richiesta',
        ];

        $validator = Validator::make($request->all(), $rules, $msg);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $error_msg = [];
            foreach ($errors->all() as $message) {
                $error_msg[] = $message;
            }

            return redirect()->back()->withErrors($error_msg);
        }
        if ($mezzo->tot_ore < $request->input('ore')) {
            // aggiorno le ore nello storico, se sono cambiate
            $so = new StoricoOre;
            $so->data = Carbon::today()->toDateString();
            $so->ore = $request->input('ore') - $mezzo->tot_ore;
            $so->mezzo_agricolo = $mezzo->id;
            $so->save();
            // aggiorno le ore del trattore
            $mezzo->tot_ore = $request->input('ore');
            $mezzo->save();
        }
        // creazione e salvataggio della manutenzione
        $nuova_manutenzione = new Manutenzione;
        $nuova_manutenzione->data = $request->input('data');
        $nuova_manutenzione->ore = $request->input('ore');
        if ($request->filled('spesa')) {
            $nuova_manutenzione->spesa = $request->input('spesa');
        } else {
            $nuova_manutenzione->spesa = 0;
        }
        $nuova_manutenzione->persona = $request->input('persona');
        if ($request->filled('straordinarie')) {
            $nuova_manutenzione->lavori_extra = $request->input('straordinarie');
        } else {
            $nuova_manutenzione->lavori_extra = null;
        }
        $nuova_manutenzione->mezzo_agricolo = $request->input('mezzo');

        try {
            $nuova_manutenzione->save();
        } catch (Throwable) {
            return redirect()->back()->withErrors('errore salvataggio manutenzione');
        }

        if ($request->filled('programmate')) {
            $nuova_manutenzione->programmate()->attach($request->get('programmate'));
        }

        return redirect()->route('agraria.index')->withSuccess("Manutenzione $mezzo->nome salvata correttamente");
    }
}
