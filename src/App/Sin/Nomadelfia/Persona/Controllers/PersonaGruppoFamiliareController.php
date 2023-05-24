<?php

namespace App\Nomadelfia\Persona\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersonaGruppoFamiliareController
{
    public function index(Request $request, $idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $attuale = $persona->gruppofamiliareAttuale();

        return view('nomadelfia.persone.gruppofamiliare.show', compact('persona', 'attuale'));
    }

    public function update(Request $request, $idPersona, $id)
    {
        $validatedData = $request->validate([
            'current_data_entrata' => 'required|date',
            'new_data_entrata' => 'required|date',
        ], [
            'current_data_entrata.date' => 'La data corrente di entrata non è una data valida',
            'current_data_entrata.required' => 'La data corrente di entrata dal gruppo è obbligatoria.',
            'new_data_entrata.required' => 'La data corrente di entrata dal gruppo è obbligatoria.',
            'new_data_entrata.date' => 'La data corrente di entrata non è una data valida',
        ]);
        $persona = Persona::findOrFail($idPersona);

        if ($persona->updateDataInizioGruppoFamiliare($id, $request->current_data_entrata, $request->new_data_entrata)) {
            return redirect()
                ->action([PersonaGruppoFamiliareController::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("Gruppo familiare $persona->nominativo modificato con successo.");
        }

        return redirect()->back()->withError('Impossibile aggiornare la data di inizio del gruppo familiare.');
    }
}
