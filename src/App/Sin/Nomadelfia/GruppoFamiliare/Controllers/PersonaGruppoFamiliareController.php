<?php

namespace App\Nomadelfia\GruppoFamiliare\Controllers;

use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaGruppoFamiliareAction;
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
        $request->validate([
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

    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'gruppo_id' => 'required',
            'data_entrata' => 'required|date',
        ], [
            'gruppo_id.required' => 'Il nuovo gruppo è obbligatorio',
            'data_entrata.required' => 'La data di entrata nel gruppo familiare è obbligatoria.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $action = new AssegnaGruppoFamiliareAction;
        $action->execute($persona, GruppoFamiliare::findOrFail($request->gruppo_id), Carbon::parse($request->data_entrata));

        return redirect()
            ->action([PersonaGruppoFamiliareController::class, 'index'], ['idPersona' => $persona->id])
            ->withSuccess("$persona->nominativo assegnato al gruppo familiare con successo");
    }

    public function delete($idPersona, $id)
    {
        $persona = Persona::findOrFail($idPersona);
        $res = $persona->gruppifamiliari()->detach($id);
        if ($res) {
            return redirect()
                ->action([PersonaGruppoFamiliareController::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("$persona->nominativo rimosso/a dal gruppo familiare con successo");
        } else {
            return redirect()->back()->withErro("Errore. Impossibile rimuovere $persona->nominativo dal gruppo familiare.");
        }
    }
}
