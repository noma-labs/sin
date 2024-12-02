<?php

namespace App\Nomadelfia\Famiglia\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersonaFamigliaController
{
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $attuale = $persona->famigliaAttuale();
        $storico = $persona->famiglieStorico;

        return view('nomadelfia.persone.famiglia.show', compact('persona', 'attuale', 'storico'));
    }

    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'nome' => 'required|unique:db_nomadelfia.famiglie,nome_famiglia',
            'posizione_famiglia' => 'required',
            'data_creazione' => 'required|date',
        ], [
            'nome.required' => 'Il nome della famiglia è obbligatorio',
            'nome.unique' => 'Il nome della famiglia esiste già',
            'posizione_famiglia.required' => 'La posizione è obbligatoria',
            'data_creazione.required' => 'Lo data di creazione della famiglia è obbligatoria',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $attuale = $persona->famigliaAttuale();
        if ($attuale) {
            return redirect()->back()->withError("La persona $persona->nominativo è già assegnata alla famiglia $attuale->nome.");
        }
        $res = $persona->createAndAssignFamiglia($idPersona, $request->posizione_famiglia, $request->nome,
            $request->data_creazione, $request->data_entrata);
        if ($res) {
            return redirect()
                ->action([PersonaFamigliaController::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("Persona $persona->nominativo e famiglia $request->nome creata con successo");
        } else {
            return redirect()->back()->withError("Impossibile creare la famiglia $request->nome");
        }
    }
}
