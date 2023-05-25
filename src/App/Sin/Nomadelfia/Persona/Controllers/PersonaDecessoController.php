<?php

namespace App\Nomadelfia\Persona\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\DecessoPersonaAction;
use Illuminate\Http\Request;

class PersonaDecessoController extends CoreBaseController
{
    public function store(Request $request, $idPersona)
    {
        $validatedData = $request->validate([
            'data_decesso' => 'required',
        ], [
            'data_decesso.required' => 'La data del decesso è obbligatorio',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $action = app(DecessoPersonaAction::class);
        $action->execute($persona, $request->data_decesso);

        return redirect()->route('nomadelfia.persone.dettaglio',
            ['idPersona' => $idPersona])->withSuccess("Il decesso di $persona->nominativo aggiornato correttamente.");
    }
}
