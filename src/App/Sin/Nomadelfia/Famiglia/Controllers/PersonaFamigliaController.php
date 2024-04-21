<?php

namespace App\Nomadelfia\Famiglia\Controllers;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
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
        $validatedData = $request->validate([
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

    // TODO: move into a dedicated controller
    public function spostaInNuovaFamiglia(Request $request, $idPersona)
    {
        $validatedData = $request->validate([
            'new_famiglia_id' => 'required',
            'new_posizione_famiglia' => 'required',
            'new_data_entrata' => 'required',
            'old_data_uscita' => 'date',
        ], [
            'new_famiglia_id.required' => 'Il nome della famiglia è obbligatorio',
            'new_posizione_famiglia.required' => 'La posizione è obbligatoria',
            'new_data_entrata.required' => 'La data di entrata nella nuova famiglia è obbligatoria',
            'old_data_entrata.date' => 'Lo data di entrata dalla famiglia non è valida',
            'old_data_uscita.date' => 'Lo data di uscita dalla famiglia non è valida',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $famiglia = Famiglia::findOrFail($request->new_famiglia_id);
        $res = $persona->spostaNellaFamiglia($famiglia, $request->new_data_entrata, $request->new_posizione_famiglia,
            $request->old_data_uscita);
        if ($res) {
            return redirect()->back()->withSuccess("Persona $persona->nominativo spostata nella famiglia con successo");
        } else {
            return redirect()->back()->withError("Impossibile spostare la persona $persona->nominativo nella famiglia");
        }
    }
}
