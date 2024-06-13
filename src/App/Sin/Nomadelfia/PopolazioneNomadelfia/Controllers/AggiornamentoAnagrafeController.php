<?php

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use Domain\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;

class AggiornamentoAnagrafeController
{
    public function index()
    {
        $activity = AggiornamentoAnagrafe::orderBy('created_at', 'DESC')->take(50)->get();

        return view('nomadelfia.aggiornamento-anagrafe.index', ['activity' => $activity]);
    }
}
