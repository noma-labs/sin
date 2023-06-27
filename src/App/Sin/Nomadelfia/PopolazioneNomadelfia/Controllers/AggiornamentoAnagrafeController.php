<?php

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;
use Spatie\Activitylog\Models\Activity;

class AggiornamentoAnagrafeController extends CoreBaseController
{
    public function index()
    {
        $entrati = AggiornamentoAnagrafe::entrati(); // Activity::inLog('nomadelfia')->ForEvent('popolazione.entrata')->orderBy('created_at', 'DESC')->take(20)->get();
        $usciti = Activity::inLog('nomadelfia')->ForEvent('popolazione.uscita')->orderBy('created_at', 'DESC')->take(20)->get();

        return view('nomadelfia.activity.popolazione', compact('entrati', 'usciti'));
    }
}
