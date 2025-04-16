<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class PersonaStatoController
{
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        return view('nomadelfia.persone.stato.show', compact('persona'));
    }

    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'stato_id' => 'required',
            'data_inizio' => 'required|date',
        ], [
            'stato_id.required' => 'Lo stato è obbligatorio',
            'data_inizio.required' => 'La data iniziale dello stato è obbligatoria.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $persona->assegnaStato($request->stato_id, $request->data_inizio, $request->data_fine);

        return redirect()
            ->action([self::class, 'index'],$persona->id)
            ->withSuccess("Stato assegnato a $persona->nominativo con successo");
    }

    public function update(Request $request, $idPersona, $id)
    {
        $request->validate([
            'data_fine' => 'date',
            'data_inizio' => 'required|date',
            'stato' => 'required',
        ], [
            'data_fine.date' => 'La data fine posizione deve essere una data valida',
            'data_inizio.required' => 'La data di inizio dello stato è obbligatoria.',
            'stato.required' => 'Lo stato attuale è obbligatorio.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $persona->stati()->updateExistingPivot($id,
            ['data_fine' => $request->data_fine, 'data_inizio' => $request->data_inizio, 'stato' => $request->stato]);

        return redirect()
            ->action([self::class, 'index'], $persona->id)
            ->withSuccess("Stato di $persona->nominativo  modificato con successo.");
    }
}
