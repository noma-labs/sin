<?php

declare(strict_types=1);

namespace App\Patente\Controllers;

use App\Patente\Models\Patente;

final class PatenteTrashedController
{
    public function index()
    {
        $patentiDeleted = Patente::withoutGlobalScope('InNomadelfia')
            ->onlyTrashed()
            ->with('persona')
            ->sortable()
            ->paginate(50);

        return view('patente.trash', compact('patentiDeleted'));
    }
}
