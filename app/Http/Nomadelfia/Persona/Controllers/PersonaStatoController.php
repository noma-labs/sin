<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Anagrafe\Persona\Models\Persona;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
final class PersonaStatoController
{
    #[Middleware('can:popolazione.persona.visualizza')]
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        return view('nomadelfia.persone.stato.show', [
            'persona' => $persona,
            'statoCivile' => $persona->statoCivile(),
            'matrimonio' => $persona->matrimonioAttuale(),
            'stati' => $persona->stati()->orderByPivot('stato', 'desc')->orderByPivot('data_inizio')->get(),
        ]);
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'stato_id' => ['required', 'exists:db_nomadelfia.stati,id'],
            'data_inizio' => ['required', 'date'],
        ], [
            'stato_id.required' => 'Lo stato è obbligatorio',
            'stato_id.exists' => 'Lo stato non esiste. Celibe, nubile, sposato e vedovo derivano dalla famiglia.',
            'data_inizio.required' => 'La data iniziale dello stato è obbligatoria.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        try {
            $persona->assegnaStato($request->stato_id, $request->data_inizio);
        } catch (Exception $e) {
            return redirect()->action([self::class, 'index'], $persona->id)->withErrors(['stato_id' => $e->getMessage()]);
        }

        return redirect()
            ->action([self::class, 'index'], $persona->id)
            ->withSuccess("Stato assegnato a $persona->nominativo con successo");
    }

    #[Middleware('can:popolazione.persona.modifica')]
    public function update(Request $request, $id, $idStato)
    {
        $request->validate([
            'data_fine' => ['nullable', 'date'],
            'data_inizio' => ['required', 'date'],
            'stato' => ['required'],
        ], [
            'data_fine.date' => 'La data fine posizione deve essere una data valida',
            'data_inizio.required' => 'La data di inizio dello stato è obbligatoria.',
            'stato.required' => 'Lo stato attuale è obbligatorio.',
        ]);
        $persona = Persona::findOrFail($id);
        $persona->stati()->updateExistingPivot($idStato,
            ['data_fine' => $request->data_fine, 'data_inizio' => $request->data_inizio, 'stato' => $request->stato]);

        return redirect()
            ->action([self::class, 'index'], $persona->id)
            ->withSuccess("Stato di $persona->nominativo  modificato con successo.");
    }
}
