<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class InternalNameController
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
            return redirect()->route('nomadelfia.person.show', $idPersona)->withSucces('Nominativo  aggiornato con suceesso');
        }

        return redirect()->route('nomadelfia.person.show', $idPersona)->withError('Errore. Il nominativo non è stato aggiornato.');

    }

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
            return redirect()->route('nomadelfia.person.show', $idPersona)->withSucces('Nuovo nominativo aggiunto con successo.');
        }

        return redirect()->route('nomadelfia.person.show', $idPersona)->withError('Errore. Il nominativo non è stato assegnato.');
    }
}
