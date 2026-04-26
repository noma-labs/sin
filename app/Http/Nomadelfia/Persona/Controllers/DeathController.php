<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\DecessoPersonaAction;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware;

#[Middleware('auth')]
final class DeathController
{
    #[Middleware('can:popolazione.persona.modifica')]
    public function store(Request $request, $id)
    {
        $request->validate([
            'data_decesso' => ['required'],
        ], [
            'data_decesso.required' => 'La data del decesso è obbligatorio',
        ]);
        $persona = Persona::findOrFail($id);
        $action = resolve(DecessoPersonaAction::class);
        $action->execute($persona, \Illuminate\Support\Facades\Date::parse($request->data_decesso));

        return to_route('nomadelfia.person.show', $id)->withSuccess("Il decesso di $persona->nominativo aggiornato correttamente.");
    }
}
