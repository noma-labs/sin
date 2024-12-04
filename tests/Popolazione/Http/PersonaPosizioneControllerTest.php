<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaPosizioneConcludiController;
use App\Nomadelfia\Persona\Controllers\PersonaPosizioneController;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;

it('can render the posizioni index page of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaPosizioneController::class, 'index'], ['idPersona' => $persona->id]))
        ->assertSuccessful();
});

it('add new posizione to a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $data = Carbon::now()->toDateString();
    $this->post(action([PersonaPosizioneController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'posizione_id' => Posizione::all()->random()->id,
            'data_inizio' => $data,
        ])->assertRedirect(route('nomadelfia.persone.posizione', ['idPersona' => $persona->id]));

});

it('update posizione of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $posizione = Posizione::all()->random();
    $data_inizio = Carbon::now()->toDateString();
    $data_fine = Carbon::now()->toDateString();
    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);

    $this->put(action([PersonaPosizioneController::class, 'update'], ['idPersona' => $persona->id, 'id' => $posizione->id]),
        [
            'current_data_inizio' => $data_inizio,
            'new_data_inizio' => Carbon::now()->addDay()->toDateString(),
        ])->assertRedirect(route('nomadelfia.persone.posizione', ['idPersona' => $persona->id]));

});

it('deletes a posizione of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $posizione = Posizione::all()->random();
    $data_inizio = Carbon::now()->toDateString();
    $data_fine = Carbon::now()->toDateString();
    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);

    $this->delete(action([PersonaPosizioneController::class, 'delete'], ['idPersona' => $persona->id, 'id' => $posizione->id]))
        ->assertRedirect(route('nomadelfia.persone.posizione', ['idPersona' => $persona->id]));

});

it('concludes a posizione of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $posizione = Posizione::all()->random();
    $data_inizio = Carbon::now()->toDateString();
    $data_fine = Carbon::now()->toDateString();
    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);

    $this->post(action([PersonaPosizioneConcludiController::class, 'store'], ['idPersona' => $persona->id, 'id' => $posizione->id]),
        [
            'data_inizio' => $data_inizio,
            'data_fine' => $data_fine,
        ])->assertRedirect(route('nomadelfia.persone.posizione', ['idPersona' => $persona->id]));

});
