<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;

final class LeaveCommunityController
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'data_uscita' => ['required'],
        ], [
            'data_uscita.required' => 'La data di uscita è obbligatoria',
        ]);
        $persona = Persona::findOrFail($id);
        if ($persona->isMoglie() or $persona->isCapofamiglia()) {
            return back()->withError("La persona $persona->nominativo non può uscire da Nomadelfia perchè risulta essere moglie o capo famiglia. Far uscire tutta la famiglia dalla pagina di gestione famiglia.");
        }
        $act = resolve(UscitaPersonaAction::class);
        $act->execute($persona, \Illuminate\Support\Facades\Date::parse($request->data_uscita), true);

        return to_route('nomadelfia.person.show', $id)->withSuccess("La data di uscita di $persona->nominativo aggiornata correttamente.");

    }

    public function update(Request $request, $idPersona, $uscita)
    {
        $request->validate([
            'data_uscita' => ['date'],
        ], [
            'data_uscita.date' => 'La data uscita non è valida.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->where('data_uscita',
            $uscita)->update(['data_uscita' => $request->data_uscita]);

        return back()->withSuccess("Data uscita di $persona->nominativo modificata con successo.");
    }
}
