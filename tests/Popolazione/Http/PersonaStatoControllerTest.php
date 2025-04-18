<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaPosizioneConcludiController;
use App\Nomadelfia\Persona\Controllers\PersonaStatoController;
use App\Nomadelfia\Persona\Controllers\PersonPositionController;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;

it('can render the stato index page of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaStatoController::class, 'index'], $persona->id))
        ->assertSuccessful();
});

it('add new stato to a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $data = Carbon::now()->toDateString();
    $this->post(action([PersonaStatoController::class, 'store'], $persona->id),
        [
            'stato_id' => Stato::all()->random()->id,
            'data_inizio' => $data,
        ])->assertRedirect(route('nomadelfia.persone.stato', $persona->id));

});

it('update stato of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $stato = Stato::all()->random();
    $data_inizio = Carbon::now()->toDateString();
    $data_fine = Carbon::now()->toDateString();
    $persona->assegnaStato($stato->id, $data_inizio, $data_fine);

    $this->put(action([PersonaStatoController::class, 'update'], ['id' => $persona->id, 'idStato' => $stato->id]),
        [
            'data_fine' => $data_fine,
            'data_inizio' => $data_inizio,
            'stato' => 1,
        ])->assertRedirect(route('nomadelfia.persone.stato', $persona->id));

});

// it('deletes a posizione of a person', function () {
//    login();
//    $persona = Persona::factory()->minorenne()->maschio()->create();
//    $posizione = Posizione::all()->random();
//    $data_inizio = Carbon::now()->toDateString();
//    $data_fine = Carbon::now()->toDateString();
//    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);
//
//    $this->delete(action([PersonPositionController::class, 'delete'], ['id' => $persona->id, 'idPos' => $posizione->id]))
//        ->assertRedirect(route('nomadelfia.person.position.index', $persona->id));
//
// });
//
// it('concludes a posizione of a person', function () {
//    login();
//    $persona = Persona::factory()->minorenne()->maschio()->create();
//    $posizione = Posizione::all()->random();
//    $data_inizio = Carbon::now()->toDateString();
//    $data_fine = Carbon::now()->toDateString();
//    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);
//
//    $this->post(action([PersonaPosizioneConcludiController::class, 'store'], ['id' => $persona->id, 'idPos' => $posizione->id]),
//        [
//            'data_inizio' => $data_inizio,
//            'data_fine' => $data_fine,
//        ])->assertRedirect(route('nomadelfia.person.position.index', $persona->id));
//
// });
