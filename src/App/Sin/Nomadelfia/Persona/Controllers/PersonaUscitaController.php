<?php

namespace App\Nomadelfia\Persona\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Illuminate\Http\Request;

class PersonaUscitaController extends CoreBaseController
{
    public function store(Request $request, $idPersona)
    {
        $validatedData = $request->validate([
            'data_uscita' => 'required',
        ], [
            'data_uscita.required' => 'La data di uscita è obbligatoria',
        ]);
        $persona = Persona::findOrFail($idPersona);
        if ($persona->isMoglie() or $persona->isCapofamiglia()) {
            return redirect()->back()->withError("La persona $persona->nominativo non può uscire da Nomadelfia perchè risulta essere moglie o capo famiglia. Far uscire tutta la famiglia dalla pagina di gestione famiglia.");
        }
        $act = app(UscitaPersonaAction::class);
        $act->execute($persona, $request->data_uscita);

        return redirect()->route('nomadelfia.persone.dettaglio',
            ['idPersona' => $idPersona])->withSuccess("La data di uscita di $persona->nominativo aggiornata correttamente.");

    }
}