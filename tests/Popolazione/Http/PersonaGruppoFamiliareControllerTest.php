<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\GruppoFamiliare\Controllers\MovePersonGruppoFamiliareController;
use App\Nomadelfia\GruppoFamiliare\Controllers\PersonGruppoFamiliareController;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Carbon\Carbon;

it('can render the gruppofamiliare index page', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonGruppoFamiliareController::class, 'index'], $persona->id))
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

    $this->put(action([PersonGruppoFamiliareController::class, 'update'], ['id' => $persona->id, 'idGruppo' => $gruppo->id]),
        [
            'current_data_entrata' => $data_entrata,
            'new_data_entrata' => $new_data_entrata,
        ])
        ->assertRedirect(route('nomadelfia.person.gruppo', $persona->id));
});

it('can assign a persona to a gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);

    $new_data_entrata = Carbon::now()->addDay()->toDatestring();

    $this->post(action([PersonGruppoFamiliareController::class, 'store'], $persona->id),
        [
            'gruppo_id' => GruppoFamiliare::all()->random()->id,
            'data_entrata' => $new_data_entrata,
        ])
        ->assertRedirect(route('nomadelfia.person.gruppo', $persona->id));
});

it('can delete a persona from a gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);

    $this->delete(action([PersonGruppoFamiliareController::class, 'delete'], ['id' => $persona->id,  'idGruppo' => $gruppo->id]))
        ->assertRedirect(route('nomadelfia.person.gruppo', $persona->id));
});

it('can move a persona from another gruppo familiare', function (): void {
    login();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);
    $new_data_entrata = Carbon::now()->addDay()->toDatestring();

    $this->post(action([MovePersonGruppoFamiliareController::class, 'store'], ['id' => $persona->id,  'idGruppo' => $gruppo->id]),
        [
            'new_gruppo_id' => GruppoFamiliare::all()->random()->id,
            'new_data_entrata' => $new_data_entrata,
            'current_data_entrata' => $data_entrata,
        ]
    )
        ->assertRedirect(route('nomadelfia.person.gruppo', $persona->id));
});
