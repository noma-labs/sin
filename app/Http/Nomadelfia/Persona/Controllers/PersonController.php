<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class PersonController
{
    public function create()
    {
        return view('nomadelfia.persone.anagrafica.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nominativo' => ['required'],
            'nome' => ['required'],
            'cognome' => ['required'],
            'data_nascita' => ['required', 'date'],
            'luogo_nascita' => ['required'],
            'sesso' => ['required'],
        ], [
            'nominativo.required' => 'Il nominativo è obbligatorio',
            'nominativo.unique' => 'IL nominativo inserito esiste già.',
            'nome.required' => 'Il nome è obbligatorie',
            'cognome.required' => 'Il cognome è obbligatorio',
            'data_nascita.required' => 'La data di nascita è obbligatoria',
            'luogo_nascita.required' => 'IL luogo di nascita è obbligatorio',
            'sesso.required' => 'Il sesso della persona è obbligatorio',
        ]);

        // TODO: check the UNIQUE constraint on the persone table
        $persona = Persona::create(
            [
                'nominativo' => $request->input('nominativo'),
                'sesso' => $request->input('sesso'),
                'nome' => $request->input('nome'),
                'cognome' => $request->input('cognome'),
                'provincia_nascita' => $request->input('luogo_nascita'),
                'data_nascita' => $request->input('data_nascita'),
                'id_arch_pietro' => 0,
            ]
        );
        $persona->save();

        return redirect(route('nomadelfia.join.create', $persona->id))->withSuccess("Dati anagrafici di $persona->nominativo inseriti correttamente.");
    }

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
