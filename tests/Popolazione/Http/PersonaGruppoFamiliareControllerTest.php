<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaGruppoFamiliareController;
use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;

it('can render the gruppofamiliare index page', function () {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaGruppoFamiliareController::class, 'index'], ['idPersona' => $persona->id]))
        ->assertSuccessful();
});

it('can update the date of a gruppo familiare', function () {
    login();
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();

    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);

    $new_data_entrata = Carbon::now()->addDay()->toDatestring();

    $this->put(action([PersonaGruppoFamiliareController::class, 'update'], ['idPersona' => $persona->id, 'id' => $gruppo->id]),
        [
            'current_data_entrata' => $data_entrata,
            'new_data_entrata' => $new_data_entrata,
        ])
        ->assertRedirect(route('nomadelfia.persone.gruppofamiliare', ['idPersona' => $persona->id]));
});
