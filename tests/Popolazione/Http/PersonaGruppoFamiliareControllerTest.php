<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\GruppoFamiliare\Controllers\PersonaGruppoFamiliareController;
use App\Nomadelfia\GruppoFamiliare\Controllers\PersonaGruppoFamiliareSpostaController;
use Carbon\Carbon;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;

it('can render the gruppofamiliare index page', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaGruppoFamiliareController::class, 'index'], ['idPersona' => $persona->id]))
        ->assertSuccessful();
});

it('can update the date of a gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
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

it('can assign a persona to a gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);

    $new_data_entrata = Carbon::now()->addDay()->toDatestring();

    $this->post(action([PersonaGruppoFamiliareController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'gruppo_id' => GruppoFamiliare::all()->random()->id,
            'data_entrata' => $new_data_entrata,
        ])
        ->assertRedirect(route('nomadelfia.persone.gruppofamiliare', ['idPersona' => $persona->id]));
});

it('can delete a persona from a gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);

    $this->delete(action([PersonaGruppoFamiliareController::class, 'delete'], ['idPersona' => $persona->id,  'id' => $gruppo->id]))
        ->assertRedirect(route('nomadelfia.persone.gruppofamiliare', ['idPersona' => $persona->id]));
});

it('can move a persona from another gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);
    $new_data_entrata = Carbon::now()->addDay()->toDatestring();

    $this->post(action([PersonaGruppoFamiliareSpostaController::class, 'store'], ['idPersona' => $persona->id,  'id' => $gruppo->id]),
        [
            'new_gruppo_id' => GruppoFamiliare::all()->random()->id,
            'new_data_entrata' => $new_data_entrata,
            'current_data_entrata' => $data_entrata,
        ]
    )
        ->assertRedirect(route('nomadelfia.persone.gruppofamiliare', ['idPersona' => $persona->id]));
});
