<?php

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;

class AggiornamentoAnagrafeController extends CoreBaseController
{
    public function index()
    {
        $activity = AggiornamentoAnagrafe::orderBy('created_at', 'DESC')->take(20)->get();
        return view('nomadelfia.aggiornamento-anagrafe.index', compact('activity'));
    }
}
