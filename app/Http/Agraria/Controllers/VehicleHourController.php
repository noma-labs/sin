<?php

declare(strict_types=1);

namespace App\Agraria\Controllers;

use App\Agraria\Models\MezzoAgricolo;
use App\Agraria\Models\StoricoOre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

final class VehicleHourController
{
    public function create()
    {
        $mezzi = MezzoAgricolo::orderBy('nome', 'asc')->get();

        return view('agraria.mezzi.hour.create', compact('mezzi'));
    }

    public function store(Request $request)
    {
        $mezzi = MezzoAgricolo::all();
        $rules = [];
        $msg = [];
        foreach ($mezzi as $m) {
            $rules['id'.$m->id] = 'required|gte:'.$m->tot_ore;
            $msg['id'.$m->id.'.required'] = 'Le ore per il mezzo '.$m->nome.' sono richieste';
            $msg['id'.$m->id.'.gte'] = 'Le ore per il mezzo '.$m->nome.' devono essere maggiori di o uguali '.$m->tot_ore;
        }

        $val = Validator::make($request->all(), $rules, $msg);
        if ($val->fails()) {
            return redirect(route('agraria.vehicle.hour.create'))->withErrors($val)->withInput();
        }

        foreach ($mezzi as $m) {
            $so = new StoricoOre;
            $so->data = \Illuminate\Support\Facades\Date::today()->toDateString();
            $so->ore = $request->input('id'.$m->id) - $m->tot_ore;
            $so->mezzo_agricolo = $m->id;

            $m->tot_ore = $request->input('id'.$m->id);
            try {
                $m->save();
                $so->save();
            } catch (Throwable) {
                $errors = collect(['Errore salvataggio ore per il mezzo '.$m->nome]);

                return to_route('agraria.vehicle.hour.create')->withErrors($errors)->withInput();
            }
        }

        return to_route('agraria.index')->withSuccess('Ore aggiornate correttamente');
    }
}
