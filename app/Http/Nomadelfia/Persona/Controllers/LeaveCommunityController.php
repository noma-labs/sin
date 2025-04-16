<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Carbon\Carbon;
use Illuminate\Http\Request;

final class LeaveCommunityController
{
    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'data_uscita' => 'required',
        ], [
            'data_uscita.required' => 'La data di uscita è obbligatoria',
        ]);
        $persona = Persona::findOrFail($idPersona);
        if ($persona->isMoglie() or $persona->isCapofamiglia()) {
            return redirect()->back()->withError("La persona $persona->nominativo non può uscire da Nomadelfia perchè risulta essere moglie o capo famiglia. Far uscire tutta la famiglia dalla pagina di gestione famiglia.");
        }
        $act = app(UscitaPersonaAction::class);
        $act->execute($persona, Carbon::parse($request->data_uscita), true);

        return redirect()->route('nomadelfia.person.show', $idPersona)->withSuccess("La data di uscita di $persona->nominativo aggiornata correttamente.");

    }

    public function update(Request $request, $idPersona, $uscita)
    {
        $request->validate([
            'data_uscita' => 'date',
        ], [
            'data_uscita.date' => 'La data uscita non è valida.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->where('data_uscita',
            $uscita)->update(['data_uscita' => $request->data_uscita]);

        return redirect()->back()->withSuccess("Data uscita di $persona->nominativo modificata con successo.");
    }
}
