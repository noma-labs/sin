<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaPosizioneConcludiController;
use App\Nomadelfia\Persona\Controllers\PersonaPosizioneController;
use App\Nomadelfia\Persona\Controllers\PersonaStatoController;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

it('can render the stato index page of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaStatoController::class, 'index'], ['idPersona' => $persona->id]))
        ->assertSuccessful();
});

it('add new stato to a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $data = Carbon::now()->toDateString();
    $this->post(action([PersonaStatoController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'stato_id' => Stato::all()->random()->id,
            'data_inizio' => $data,
        ])->assertRedirect(route('nomadelfia.persone.stato', ['idPersona' => $persona->id]));

});

it('update stato of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $stato = Stato::all()->random();
    $data_inizio = Carbon::now()->toDateString();
    $data_fine = Carbon::now()->toDateString();
    $persona->assegnaStato($stato->id, $data_inizio, $data_fine);

    $this->put(action([PersonaStatoController::class, 'update'], ['idPersona' => $persona->id, 'id' => $stato->id]),
        [
            'data_fine' => $data_fine,
            'data_inizio' => $data_inizio,
            'stato' => 1,
        ])->assertRedirect(route('nomadelfia.persone.stato', ['idPersona' => $persona->id]));

});

//it('deletes a posizione of a person', function () {
//    login();
//    $persona = Persona::factory()->minorenne()->maschio()->create();
//    $posizione = Posizione::all()->random();
//    $data_inizio = Carbon::now()->toDateString();
//    $data_fine = Carbon::now()->toDateString();
//    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);
//
//    $this->delete(action([PersonaPosizioneController::class, 'delete'], ['idPersona' => $persona->id, 'id' => $posizione->id]))
//        ->assertRedirect(route('nomadelfia.persone.posizione', ['idPersona' => $persona->id]));
//
//});
//
//it('concludes a posizione of a person', function () {
//    login();
//    $persona = Persona::factory()->minorenne()->maschio()->create();
//    $posizione = Posizione::all()->random();
//    $data_inizio = Carbon::now()->toDateString();
//    $data_fine = Carbon::now()->toDateString();
//    $persona->assegnaPosizione($posizione->id, $data_inizio, $data_fine);
//
//    $this->post(action([PersonaPosizioneConcludiController::class, 'store'], ['idPersona' => $persona->id, 'id' => $posizione->id]),
//        [
//            'data_inizio' => $data_inizio,
//            'data_fine' => $data_fine,
//        ])->assertRedirect(route('nomadelfia.persone.posizione', ['idPersona' => $persona->id]));
//
//});
