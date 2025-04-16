<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;

final class JoinLeaveHistoryController
{
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        $attuale = PopolazioneNomadelfia::where('persona_id', $idPersona)->whereNull('data_uscita')->first();
        $storico = PopolazioneNomadelfia::where('persona_id', $idPersona)->whereNotNull('data_uscita')->orderby('data_entrata')->get();

        return view('nomadelfia.persone.popolazione.show', compact('persona', 'attuale', 'storico'));
    }
}
