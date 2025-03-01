<?php

declare(strict_types=1);

namespace App\Agraria\Controllers;

use App\Agraria\Models\Manutenzione;
use App\Agraria\Models\MezzoAgricolo;
use Illuminate\Http\Request;

final class SearchableMaintenanceController
{
    public function show(Request $request)
    {
        $vehichles = MezzoAgricolo::orderBy('nome', 'asc')->get();

        $query = Manutenzione::with('mezzo')->orderBy('data', 'desc');
        if ($request->filled('vehicle')) {
            $query->where('mezzo_agricolo', $request->input('vehicle'));
        }
        if ($request->filled('from')) {
            $query->where('data', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            dd($request->input('to'));
            $query->where('data', '<=', $request->input('to'));
        }
        if ($request->filled('term')) {
            $query->where('lavori_extra', 'like', "%{$request->input('term')}%");
        }
        $maintenances = $query->paginate(20);

        return view('agraria.maintenanance.search', compact('vehichles', 'maintenances'));
    }
}
