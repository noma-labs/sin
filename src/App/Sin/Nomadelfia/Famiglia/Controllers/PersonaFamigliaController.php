<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Controllers;

use App\Nomadelfia\Persona\Models\Persona;

final class PersonaFamigliaController
{
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $attuale = $persona->famigliaAttuale();
        $storico = $persona->famiglieStorico;

        return view('nomadelfia.persone.famiglia.show', compact('persona', 'attuale', 'storico'));
    }
}
