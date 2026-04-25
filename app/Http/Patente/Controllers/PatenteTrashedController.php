<?php

declare(strict_types=1);

namespace App\Patente\Controllers;

use App\Patente\Models\Patente;
use Illuminate\Contracts\View\View;

final class PatenteTrashedController
{
    public function index(): View
    {
        $patentiDeleted = Patente::onlyTrashed()
            ->withoutGlobalScope('InNomadelfia')
            ->sortable()
            ->with('persona')
            ->paginate(50);

        return view('patente.trash', compact('patentiDeleted'));
    }
}
