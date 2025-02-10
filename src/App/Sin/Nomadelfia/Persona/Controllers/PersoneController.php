<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;

final class PersoneController
{
    public function show($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $posizioneAttuale = $persona->posizioneAttuale();
        $gruppoAttuale = $persona->gruppofamiliareAttuale();
        $famigliaAttuale = $persona->famigliaAttuale();

        return view('nomadelfia.persone.show',
            compact('persona', 'posizioneAttuale', 'gruppoAttuale', 'famigliaAttuale'));
    }

    public function delete($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        if ($persona->delete()) {
            return redirect()->route('nomadelfia.index')->withSuccess("Persona $persona->nominativo eliminata con successo");
        }

        return view('nomadelfia')->withError("Impossibile eliminare $persona->nominativo ");
    }
}
