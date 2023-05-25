<?php

namespace App\Nomadelfia\GruppoFamiliare\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersonaGruppoFamiliareConcludiController
{
    public function store(Request $request, $idPersona, $id)
    {
        $validatedData = $request->validate([
            'data_entrata' => 'required|date',
            'data_uscita' => 'required|date|after_or_equal:data_entrata',
        ], [
            'data_entrata.date' => 'La data di entrata non è una data valida',
            'data_entrata.required' => 'La data di entrata è obbligatoria',
            'data_entrata.date' => 'La data di uscita non è  una data valida',
            'data_entrata.required' => 'La data di uscota non è  una data valida',
            'data_uscita.after_or_equal' => 'La data di uscita non può essere inferiore alla data di entrata',
        ]);

        $persona = Persona::findOrFail($idPersona);
        $res = $persona->concludiGruppoFamiliare($id, $request->data_entrata, $request->data_uscita);
        if ($res) {
            return redirect()
                ->action([PersonaGruppoFamiliareController::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("$persona->nominativo rimosso/a dal gruppo familiare con successo");
        } else {
            return redirect()->back()->withError("Errore. Impossibile rimuovere $persona->nominativo dal gruppo familiare.");
        }
    }
}
