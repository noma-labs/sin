<?php

namespace App\Nomadelfia\Famiglia\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;

class PersonaFamigliaController
{
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $attuale = $persona->famigliaAttuale();
        $storico = $persona->famiglieStorico;

        return view('nomadelfia.persone.famiglia.show', compact('persona', 'attuale', 'storico'));
    }
}
