<?php

namespace App\Nomadelfia\Persona\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersonaPosizioneConcludiController
{
    public function store(Request $request, $idPersona, $id)
    {
        $request->validate([
            'data_inizio' => 'required|date',
            'data_fine' => 'required|date|after_or_equal:data_inizio',
        ], [
            'data_inizio.date' => 'La data di entrata non è  una data valida',
            'data_inizio.required' => 'La data di entrata è obbligatoria',
            'data_fine.date' => 'La data di uscita non è  una data valida',
            'data_fine.required' => 'La data di uscita  è obbligatoria',
            'data_fine.after_or_equal' => 'La data di fine posizione non può essere inferiore alla data di inizio',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $res = $persona->concludiPosizione($id, $request->data_inizio, $request->data_fine);
        if ($res) {
            return redirect()
                ->action(function ($idPersona) {
                    return (new \App\Nomadelfia\Persona\Controllers\PersonaPosizioneController())->index($idPersona);
                }, ['idPersona' => $persona->id])
                ->withSuccess("Posizione di $persona->nominativo aggiornata con successo");
        } else {
            return redirect()->back()->withError("Errore. Impossibile aggiornare la posizione di  $persona->nominativo");
        }
    }
}
