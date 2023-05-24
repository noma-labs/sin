<?php

namespace App\Nomadelfia\Persona\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Nomadelfia\GruppoFamiliare\Controllers\PersonaGruppoFamiliareController;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Illuminate\Http\Request;

class PersonaPosizioneController extends CoreBaseController
{
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $posattuale = $persona->posizioneAttuale();
        $storico = $persona->posizioniStorico;

        return view('nomadelfia.persone.posizione.show', compact('persona', 'posattuale', 'storico'));
    }

    public function store(Request $request, $idPersona)
    {
        $validatedData = $request->validate([
            'posizione_id' => 'required',
            'data_inizio' => 'required|date',
            //"data_fine" => "date",
        ], [
            'posizione_id.required' => 'La posizione è obbligatorio',
            'data_inizio.required' => 'La data di inizio della posizione è obbligatoria.',
            // 'data_fine.required'=>"La data fine della posizione è obbligatoria.",
        ]);
        $persona = Persona::findOrFail($idPersona);
        $persona->assegnaPosizione($request->posizione_id, $request->data_inizio, $request->data_fine);

        return redirect()
            ->action([PersonaPosizioneController::class, 'index'], ['idPersona' => $persona->id])
            ->withSuccess("Nuova posizione assegnata a $persona->nominativo  con successo.");
    }

    public function update(Request $request, $idPersona, $id)
    {
        $validatedData = $request->validate([
            'current_data_inizio' => 'required',
            'new_data_inizio' => 'required|date',
        ], [
            'new_data_inizio.date' => 'La nuova data di inzio posizione non è una data valida',
            'new_data_inizio.required' => 'La nuova data di inizio della posizione è obbligatoria.',
            'current_data_inizio.required' => 'La data di inizio della posizione è obbligatoria.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        if ($persona->modificaDataInizioPosizione($id, $request->current_data_inizio, $request->new_data_inizio)) {
            return redirect()
                ->action([PersonaPosizioneController::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("Posizione modificata di $persona->nominativo con successo");
        }

        return redirect()->back()->withError("Impossibile aggiornare la posizione di  $persona->nominativo");
    }

    public function delete($idPersona, $id)
    {
        $persona = Persona::findOrFail($idPersona);
        $res = $persona->posizioni()->detach($id);
        if ($res) {
            return redirect()
                ->action([PersonaPosizioneController::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("Posizione rimossa consuccesso per $persona->nominativo ");
        } else {
            return redirect()->back()->withErro("Errore. Impossibile rimuovere la posizione per $persona->nominativo");
        }
    }
}
