<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class InternalNameController
{
    public function edit($id)
    {
        $persona = Persona::findOrFail($id);

        return view('nomadelfia.persone.edit_nominativo', compact('persona'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nominativo' => ['required', 'unique:db_nomadelfia.persone,nominativo'],
        ], [
            'nominativo.required' => 'Il nominativo è obbligatorio',
            'nominativo.unique' => "Il nominativo $request->nominativo assegnato ad un'altra persona.",
        ]);
        $persona = Persona::findOrFail($id);
        $persona->nominativo = $request->nominativo;
        if ($persona->save()) {
            return to_route('nomadelfia.person.show', $id)->withSucces('Nominativo  aggiornato con suceesso');
        }

        return to_route('nomadelfia.person.show', $id)->withError('Errore. Il nominativo non è stato aggiornato.');

    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'nuovonominativo' => ['required', 'unique:db_nomadelfia.persone,nominativo'],
        ], [
            'nuovonominativorequired' => 'Il nominativo è obbligatorio',
            'nuovonominativounique' => "Il nominativo $request->nominativo assegnato ad un'altra persona.",
        ]);
        $persona = Persona::findOrFail($id);
        $persona->nominativiStorici()->create(['nominativo' => $persona->nominativo]);
        $persona->nominativo = $request->nuovonominativo;
        if ($persona->save()) {
            return to_route('nomadelfia.person.show', $id)->withSucces('Nuovo nominativo aggiunto con successo.');
        }

        return to_route('nomadelfia.person.show', $id)->withError('Errore. Il nominativo non è stato assegnato.');
    }
}
