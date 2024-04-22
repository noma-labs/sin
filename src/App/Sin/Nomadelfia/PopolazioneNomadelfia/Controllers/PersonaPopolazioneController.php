<?php

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;

class PersonaPopolazioneController
{
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        $attuale = PopolazioneNomadelfia::where('persona_id', $idPersona)->whereNull('data_uscita')->first();
        $storico = PopolazioneNomadelfia::where('persona_id', $idPersona)->whereNotNull('data_uscita')->orderby('data_entrata')->get();

        return view('nomadelfia.persone.popolazione.show', compact('persona', 'attuale', 'storico'));
    }
}
