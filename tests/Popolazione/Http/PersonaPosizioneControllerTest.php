<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaPosizioneConcludiController;
use App\Nomadelfia\Persona\Controllers\PersonPositionController;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Carbon\Carbon;

it('can render the posizioni index page of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonPositionController::class, 'index'], $persona->id))
        ->assertSuccessful();
});

it('add new posizione to a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $data = Carbon::now()->startOfDay();
    $this->post(action([PersonPositionController::class, 'store'], $persona->id),
        [
            'posizione_id' => Posizione::all()->random()->id,
            'data_inizio' => $data,
        ])->assertRedirect(route('nomadelfia.person.position.index', $persona->id));

});

it('update posizione of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $posizione = Posizione::all()->random();
    $data_inizio = Carbon::now()->startOfDay();
    $data_fine = Carbon::now()->startOfDay();
    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);

    $this->put(action([PersonPositionController::class, 'update'], ['idPersona' => $persona->id, 'id' => $posizione->id]),
        [
            'current_data_inizio' => $data_inizio->toDateString(),
            'new_data_inizio' => Carbon::now()->addDay()->toDateString(),
        ])->assertRedirect(route('nomadelfia.person.position.index', $persona->id));

});

it('deletes a posizione of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $posizione = Posizione::all()->random();
    $data_inizio = Carbon::now()->startOfDay();
    $data_fine = Carbon::now()->startOfDay();
    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);

    $this->delete(action([PersonPositionController::class, 'delete'], ['idPersona' => $persona->id, 'id' => $posizione->id]))
        ->assertRedirect(route('nomadelfia.person.position.index', $persona->id));

});

it('concludes a posizione of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $posizione = Posizione::all()->random();
    $data_inizio = Carbon::now()->startOfDay();
    $data_fine = Carbon::now()->startOfDay();
    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);

    $this->post(action([PersonaPosizioneConcludiController::class, 'store'], ['idPersona' => $persona->id, 'id' => $posizione->id]),
        [
            'data_inizio' => $data_inizio,
            'data_fine' => $data_fine,
        ])->assertRedirect(route('nomadelfia.person.position.index', $persona->id));

});
