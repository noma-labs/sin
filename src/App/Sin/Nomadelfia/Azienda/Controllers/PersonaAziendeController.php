<?php

namespace App\Nomadelfia\Azienda\Controllers;

use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;
use Illuminate\Http\Request;

class PersonaAziendeController
{
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        return view('nomadelfia.persone.aziende.show', compact('persona'));
    }

    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'azienda_id' => 'required',
            'mansione' => 'required',
            'data_inizio' => 'required|date',
        ], [
            'azienda_id.required' => "L'azienda è obbligatoria",
            'data_inizio.required' => "La data di inizio dell'azienda è obbligatoria.",
            'mansione.required' => "La mansione del lavoratore nell'azienda è obbligatoria.",

        ]);
        $persona = Persona::findOrFail($idPersona);
        $azienda = Azienda::findOrFail($request->azienda_id);
        $action = new AssegnaAziendaAction;
        if (strcasecmp($request->mansione, 'lavoratore') == 0) {
            $action->execute($persona, $azienda, Carbon::parse($request->data_inizio), Azienda::MANSIONE_LAVORATORE);

            return redirect()
                ->action([PersonaAziendeController::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("$persona->nominativo assegnato all'azienda $azienda->nome_azienda come $request->mansione con successo");
        }
        if (strcasecmp($request->mansione, 'responsabile azienda') == 0) {
            $action->execute($persona, $azienda, Carbon::parse($request->data_inizio), Azienda::MANSIONE_RESPONSABILE);

            return redirect()
                ->action([PersonaAziendeController::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("$persona->nominativo assegnato all'azienda $azienda->nome_azienda come $request->mansione con successo");
        }

        return redirect()->back()->withError("La mansione $request->mansione non riconosciuta.");
    }

    public function update(Request $request, $idPersona, $id)
    {
        $request->validate([
            'mansione' => 'required',
            'data_entrata' => 'required|date',
            'stato' => 'required',
        ], [
            'data_entrata.required' => "La data di inizio dell'azienda è obbligatoria.",
            'mansione.required' => "La mansione del lavoratore nell'azienda è obbligatoria.",
            'stato.required' => 'Lo stato è obbligatoria.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $azienda = Azienda::findOrFail($id);
        $persona->aziende()->updateExistingPivot($id, [
            'stato' => $request->stato,
            'data_inizio_azienda' => $request->data_entrata,
            'data_fine_azienda' => $request->data_uscita,
            'mansione' => $request->mansione,
        ]);

        return redirect()
            ->action([PersonaAziendeController::class, 'index'], ['idPersona' => $persona->id])
            ->withSuccess("Azienda $azienda->nome_azienda di $persona->nominativo  modificata con successo.");
    }
}
