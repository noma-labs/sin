<?php

namespace App\Nomadelfia\GruppoFamiliare\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersonaGruppoFamiliareSpostaController
{
    /**
     * Sposta una persona da un gruppo familiare ad un  nuovo gruppo familiare settando al data di entrata e uscita
     *
     * @author Davide Neri
     */
    public function store(Request $request, $idPersona, $id)
    {
        $validatedData = $request->validate([
            'new_gruppo_id' => 'required',
            'new_data_entrata' => 'required|date', // data entrata delnuovo gruppo familiare
            'current_data_entrata' => 'required|date', // data entrata del vecchio gruppo familiare
        ], [
            'new_gruppo_id.required' => 'Il nuovo gruppo è obbligatorio',
            'new_data_entrata.required' => 'La data di entrata nel nuovo gruppo familiare è obbligatoria.',
            'current_data_entrata.required' => 'La data di entrata nel gruppo familiare è obbligatoria.',
        ]);
        // se la data  di uscita del nuovo gruppo non è stata indicata, viene settata uguale all data di entrata nel nuovo gruppo
        $new_datain = $request->new_data_entrata;
        $current_data_uscita = $request->input('current_data_uscita', $new_datain);
        $persona = Persona::findOrFail($idPersona);

        $persona->spostaPersonaInGruppoFamiliare($id, $request->current_data_entrata, $current_data_uscita,
            $request->new_gruppo_id, $new_datain);

        return redirect()
            ->action([PersonaGruppoFamiliareController::class, 'index'], ['idPersona' => $persona->id])
            ->withSuccess("$persona->nominativo assegnato al gruppo familiare con successo");
    }
}
