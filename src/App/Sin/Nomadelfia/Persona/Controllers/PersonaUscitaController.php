<?php

namespace App\Nomadelfia\Persona\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;

class PersonaUscitaController
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
        $act->execute($persona, $request->data_uscita, true);

        return redirect()->route('nomadelfia.persone.dettaglio',
            ['idPersona' => $idPersona])->withSuccess("La data di uscita di $persona->nominativo aggiornata correttamente.");

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
