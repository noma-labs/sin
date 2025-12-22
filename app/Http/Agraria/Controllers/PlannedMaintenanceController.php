<?php

declare(strict_types=1);

namespace App\Agraria\Controllers;

use App\Agraria\Models\ManutenzioneProgrammata;
use App\Agraria\Models\MezzoAgricolo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

final class PlannedMaintenanceController
{
    public function index()
    {
        $planned = ManutenzioneProgrammata::orderBy('ore', 'asc')->get();

        return view('agraria.maintenanance.planned.index', compact('planned'));
    }

    public function store(Request $request): array
    {
        $rules = [
            'data.nome' => 'required',
            'data.ore' => 'required|gte:0',
        ];

        $msg = [
            'data.nome.required' => 'Il nome della manutenzione è richiesto',
            'data.ore.required' => 'Le ore della manutenzione sono richieste',
            'data.ore.gte' => 'Le ore della manutenzione devono essere maggiori o uguali a 0',
        ];

        $validator = Validator::make($request->all(), $rules, $msg);
        if ($validator->fails()) {
            return ['result' => 'error', 'msg' => $validator->errors()];
        }
        if ($request->filled('data.id')) {
            $man = ManutenzioneProgrammata::find($request->input('data.id'));

            $man->nome = mb_strtoupper((string) $request->input('data.nome'));
            $man->ore = $request->input('data.ore');
            $man->save();
        }

        return to_route('agraria.maintenanance.planned.index')->withSuccess('Manutenzione aggiunta correttamente');
    }

    public function ricerca(Request $request)
    {
        if ($request->filled('id')) {
            $mezzo = MezzoAgricolo::find($request->input('id'));

            return view('agraria.manutenzioni.ricerca', compact('mezzo'));
        }

        return view('agraria.manutenzioni.ricerca');
    }
}
