<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\DB;

final class PersonController
{
    public function show($id)
    {
        $persona = Persona::findOrFail($id);
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
        $persona->delete();
        return redirect()->route('nomadelfia.index')->withSuccess("Persona $persona->nominativo eliminata con successo");
    }
}
