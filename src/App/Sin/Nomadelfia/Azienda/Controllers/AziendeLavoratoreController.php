<?php

declare(strict_types=1);

namespace App\Nomadelfia\Azienda\Controllers;

use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;
use Illuminate\Http\Request;

final class AziendeLavoratoreController
{

    public function store(Request $request, $id)
    {
        $request->validate([
            'persona_id' => 'required',
            'data_inizio' => 'required|date',
        ], [
            'persona_id.required' => 'La persona è obbligatoria.',
            'data_inizio.required' => "La data di inizio dell'azienda è obbligatoria.",
        ]);

        $persona = Persona::findOrFail($request->input('persona_id'));
        $azienda = Azienda::findOrFail($id);
        $action = new AssegnaAziendaAction;
        $action->execute($persona, $azienda, Carbon::parse($request->input('data_inizio')), Azienda::MANSIONE_LAVORATORE);

        return redirect()->back()->withSuccess("Persona $persona->nominativo  aggiunto a {$azienda->nome_azienda} con successo.");
    }

    public function sposta(Request $request, $id, $idPersona)
    {
        $request->validate([
            'nuova_azienda_id' => 'required',
        ], [
            'nuova_azienda_id.required' => 'La nuova azienda è obbligatoria.',
        ]);
        $azienda = Azienda::findOrFail($id);
        $persona = Persona::findOrFail($idPersona);
        $nuova_azienda = Azienda::findOrFail($request->input('nuova_azienda_id'));

        $azienda->lavoratori()->updateExistingPivot($persona->id, ['stato' => 'Non Attivo', 'data_fine_azienda' => $request->input('data_fine')]);
        $nuova_azienda->lavoratori()->attach($persona->id, ['data_inizio_azienda' => $request->input('data_fine')]);

        return redirect()->back()->withSuccess("Persona $persona->nominativo  aggiunto a {$nuova_azienda->nome_azienda} con successo.");
    }

    public function update(Request $request, $id, $idPersona)
    {
        $request->validate([
            'mansione' => 'required',
            'data_inizio' => 'required|date',
        ], [
            'data_inizio.required' => "La data di inizio dell'azienda è obbligatoria.",
            'mansione.required' => "La mansione del lavoratore nell'azienda è obbligatoria.",
        ]);

        $persona = Persona::findOrFail($idPersona);
        $azienda = Azienda::findOrFail($id);

        $persona->aziende()->updateExistingPivot($id, [
            'mansione' => $request->mansione,
            'data_inizio_azienda' => $request->data_inizio,
        ]);

        return redirect()->back()->withSuccess("Azienda $azienda->nome_azienda modificata con successo.");
    }

    public function delete($id, $idPersona)
    {
        $azienda = Azienda::findOrFail($id);
        $persona = Persona::findOrFail($idPersona);
        $azienda->lavoratori()->updateExistingPivot($persona->id, ['stato' => 'Non Attivo', 'data_fine_azienda' => Carbon::now()]);

        return redirect()->back()->withSuccess("Persona rimossa dall'azienda {$azienda->nome_azienda} con successo.");
    }
}
