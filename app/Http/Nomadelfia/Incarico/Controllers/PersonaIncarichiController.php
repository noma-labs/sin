<?php

declare(strict_types=1);

namespace App\Nomadelfia\Incarico\Controllers;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Incarico\Models\Incarico;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaIncaricoAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\Rule;

/**
 * The incarichi of a persona, assigned from her profile choosing the incarico.
 */
#[Middleware('auth')]
final class PersonaIncarichiController
{
    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(Request $request, string $idPersona, AssegnaIncaricoAction $assegnaIncarico): RedirectResponse
    {
        $persona = Persona::query()->findOrFail($idPersona);
        $request->validate([
            'incarico_id' => [
                'required',
                Rule::exists('db_nomadelfia.incarichi', 'id'),
                Rule::notIn($persona->incarichiAttuali()->pluck('id')),
            ],
            'data_inizio' => ['required', 'date'],
        ], [
            'incarico_id.required' => "L'incarico è obbligatorio.",
            'incarico_id.not_in' => "$persona->nominativo ha già questo incarico.",
            'data_inizio.required' => "La data di inizio dell'incarico è obbligatoria.",
        ]);

        $incarico = Incarico::query()->findOrFail($request->integer('incarico_id'));
        $assegnaIncarico->execute($persona, $incarico, Date::parse($request->string('data_inizio')->toString()));

        return back()->with('success', "$persona->nominativo aggiunto all'incarico $incarico->nome con successo.");
    }
}
