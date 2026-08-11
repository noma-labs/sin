<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\Persona\Requests\StorePersonaRequest;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

#[Middleware('auth')]
final class PersonController
{
    #[Middleware('can:popolazione.persona.inserisci')]
    public function create()
    {
        return view('nomadelfia.persone.anagrafica.create');
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(StorePersonaRequest $request)
    {
        $persona = Persona::create([
            'nominativo' => $request->input('nominativo'),
            'sesso' => $request->input('sesso'),
            'nome' => $request->input('nome'),
            'cognome' => $request->input('cognome'),
            'provincia_nascita' => $request->input('luogo_nascita'),
            'data_nascita' => $request->input('data_nascita'),
            'id_arch_pietro' => 0,
        ]);

        return redirect(route('nomadelfia.join.create', $persona->id))->withSuccess("Dati anagrafici di $persona->nominativo inseriti correttamente.");
    }

    #[Middleware('can:popolazione.persona.visualizza')]
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
}
