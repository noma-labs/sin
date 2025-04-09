<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\DB;

final class PersoneController
{
    public function show($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $posizioneAttuale = $persona->posizioneAttuale();
        $gruppoAttuale = $persona->gruppofamiliareAttuale();
        $famigliaAttuale = $persona->famigliaAttuale();

        $famigliaEnrico = DB::connection('db_nomadelfia')
            ->table('alfa_enrico_15_feb_23')
            ->select('famiglia')
            ->where('id', $persona->id_alfa_enrico)
            ->first();

        return view('nomadelfia.persone.show',
            compact('persona', 'posizioneAttuale', 'gruppoAttuale', 'famigliaAttuale', 'famigliaEnrico'));
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
