<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Controllers\PersonaPosizioneConcludiController;
use App\Nomadelfia\Persona\Controllers\PersonPositionController;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
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

it('gives a new posizione from the profile, ending the posizione attuale the same day, and goes back to the profile', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2020-01-10'), GruppoFamiliare::first());

    $this->from(route('nomadelfia.person.profile', $persona->id))
        ->post(route('nomadelfia.person.position.store', $persona->id), [
            'posizione_id' => Posizione::find('POST')->id,
            'data_inizio' => '2022-06-01',
            'torna_indietro' => '1',
        ])
        ->assertRedirect(route('nomadelfia.person.profile', $persona->id))
        ->assertSessionHasNoErrors();

    expect($persona->posizioneAttuale()->abbreviato)->toBe('POST')
        ->and($persona->posizioniStorico()->sole()->pivot->data_fine)->toBe('2022-06-01');
});

it('update posizione of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $posizione = Posizione::all()->random();
    $data_inizio = Carbon::now()->startOfDay();
    $data_fine = Carbon::now()->startOfDay();
    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);

    $this->put(action([PersonPositionController::class, 'update'], ['id' => $persona->id, 'idPos' => $posizione->id]),
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

    $this->delete(action([PersonPositionController::class, 'delete'], ['id' => $persona->id, 'idPos' => $posizione->id]))
        ->assertRedirect(route('nomadelfia.person.position.index', $persona->id));

});

it('concludes a posizione of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $posizione = Posizione::all()->random();
    $data_inizio = Carbon::now()->startOfDay();
    $data_fine = Carbon::now()->startOfDay();
    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);

    $this->post(action([PersonaPosizioneConcludiController::class, 'store'], ['id' => $persona->id, 'idPos' => $posizione->id]),
        [
            'data_inizio' => $data_inizio,
            'data_fine' => $data_fine,
        ])->assertRedirect(route('nomadelfia.person.position.index', $persona->id));

});
