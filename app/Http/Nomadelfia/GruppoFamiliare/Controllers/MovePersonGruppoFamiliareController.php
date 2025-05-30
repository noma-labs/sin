<?php

declare(strict_types=1);

namespace App\Nomadelfia\GruppoFamiliare\Controllers;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\ChangeGruppoFamiliareAction;
use Carbon\Carbon;
use Illuminate\Http\Request;

final class MovePersonGruppoFamiliareController
{
    public function store(Request $request, $id, $idGruppo)
    {
        $request->validate([
            'new_gruppo_id' => 'required',
            'new_data_entrata' => 'required|date', // data entrata delnuovo gruppo familiare
            'current_data_entrata' => 'required|date', // data entrata del vecchio gruppo familiare
        ], [
            'new_gruppo_id.required' => 'Il nuovo gruppo è obbligatorio',
            'new_data_entrata.required' => 'La data di entrata nel nuovo gruppo familiare è obbligatoria.',
            'current_data_entrata.required' => 'La data di entrata nel gruppo familiare è obbligatoria.',
        ]);
        // se la data  di uscita del nuovo gruppo non è stata indicata, viene settata uguale all data di entrata nel nuovo gruppo
        $new_datain = $request->new_data_entrata;
        $current_data_uscita = $request->input('current_data_uscita', $new_datain);
        $persona = Persona::findOrFail($id);

        $action = new ChangeGruppoFamiliareAction;
        $action->execute($persona,
            GruppoFamiliare::findOrFail($idGruppo),
            Carbon::parse($request->current_data_entrata),
            Carbon::parse($current_data_uscita),
            GruppoFamiliare::findOrFail($request->new_gruppo_id),
            Carbon::parse($new_datain),
        );

        return redirect()
            ->action([PersonGruppoFamiliareController::class, 'index'], $persona->id)
            ->withSuccess("$persona->nominativo assegnato al gruppo familiare con successo");
    }
}
