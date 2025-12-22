<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class PersonIdentityController
{
    public function edit($id)
    {
        $persona = Persona::findOrFail($id);

        return view('nomadelfia.persone.anagrafica.edit', compact('persona'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => ['required'],
            'cognome' => ['required'],
            'datanascita' => ['required'],
            //            "luogonascita" => "required",
            'sesso' => ['required'],
        ], [
            'nome.required' => 'Il nome è obbligatorie',
            'cognome.required' => 'Il cognome è obbligatorio',
            'datanascita.required' => 'La data di nascita è obbligatoria',
            //            "luogonascita.required" => "Il luogo di nascita è obbligatorio",
            'sesso.required' => 'Il sesso è obbligatorio',
        ]);
        $persona = Persona::findOrFail($id);
        $persona->nome = $request->nome;
        $persona->cognome = $request->cognome;
        $persona->data_nascita = $request->datanascita;
        $persona->cf = $request->codicefiscale;
        $persona->provincia_nascita = $request->luogonascita;
        $persona->sesso = $request->sesso;
        $persona->biografia = $request->get('biografia', $persona->biografia);
        $persona->data_decesso = $request->get('data_decesso', $persona->data_decesso);
        if ($persona->save()) {
            return to_route('nomadelfia.person.show', $id)->withSuccess("Dati anagrafici di $persona->nominativo aggiornati correttamente. ");
        }

        return to_route('nomadelfia.person.show', $id)->withError("Errore dureante l'aggiornamente dei dati anagrafici di $persona->nominativo.");

    }
}
