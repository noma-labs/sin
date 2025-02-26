<?php

declare(strict_types=1);

namespace App\Agraria\Controllers;

use App\Agraria\Models\MezzoAgricolo;

final class SearchableMaintenanceController
{
    public function show()
    {
        $mezzi = MezzoAgricolo::orderBy('nome', 'asc')->get();

        return view('agraria.maintenanance.search', compact('mezzi'));
    }
}
