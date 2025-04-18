<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;

final class RecentActivitesController
{
    public function index()
    {
        $activity = AggiornamentoAnagrafe::orderBy('created_at', 'DESC')->take(50)->get();

        return view('nomadelfia.aggiornamento-anagrafe.index', compact('activity'));
    }
}
