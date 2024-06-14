<?php

namespace App\Nomadelfia\Persona\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersonaNominativoController
{
    public function edit($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        return view('nomadelfia.persone.edit_nominativo', compact('persona'));
    }

    public function update(Request $request, $idPersona)
    {
        $request->validate([
            'nominativo' => 'required|unique:db_nomadelfia.persone,nominativo',
        ], [
            'nominativo.required' => 'Il nominativo è obbligatorio',
            'nominativo.unique' => "Il nominativo $request->nominativo assegnato ad un'altra persona.",
        ]);
        $persona = Persona::findOrFail($idPersona);
        $persona->nominativo = $request->nominativo;
        if ($persona->save()) {
            return redirect()->route('nomadelfia.persone.dettaglio',
                ['idPersona' => $idPersona])->withSucces('Nominativo  aggiornato con suceesso');
        } else {
            return redirect()->route('nomadelfia.persone.dettaglio',
                ['idPersona' => $idPersona])->withError('Errore. Il nominativo non è stato aggiornato.');
        }
    }

    /**
     * Assegna un nuovo nominativo e salva il nominativo attuale nello storico dei nominativi.
     *
     * @author Davide Neri
     */
    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'nuovonominativo' => 'required|unique:db_nomadelfia.persone,nominativo',
        ], [
            'nuovonominativorequired' => 'Il nominativo è obbligatorio',
            'nuovonominativounique' => "Il nominativo $request->nominativo assegnato ad un'altra persona.",
        ]);
        $persona = Persona::findOrFail($idPersona);
        $persona->nominativiStorici()->create(['nominativo' => $persona->nominativo]);
        $persona->nominativo = $request->nuovonominativo;
        if ($persona->save()) {
            return redirect()->route('nomadelfia.persone.dettaglio',
                ['idPersona' => $idPersona])->withSucces('Nuovo nominativo aggiunto con successo.');
        } else {
            return redirect()->route('nomadelfia.persone.dettaglio',
                ['idPersona' => $idPersona])->withError('Errore. Il nominativo non è stato assegnato.');
        }
    }
}
