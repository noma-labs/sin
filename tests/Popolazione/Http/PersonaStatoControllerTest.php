<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Persona\Controllers\PersonaPosizioneConcludiController;
use App\Nomadelfia\Persona\Controllers\PersonaStatoController;
use App\Nomadelfia\Persona\Controllers\PersonPositionController;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

it('can render the stato index page of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaStatoController::class, 'index'], $persona->id))
        ->assertSuccessful();
});

it('adds a stato to a person keeping the other active stati', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $sacerdote = Stato::perNome('sacerdote');
    $separato = Stato::perNome('separato');
    $persona->assegnaStato($sacerdote, '1980-03-12');

    $this->post(action([PersonaStatoController::class, 'store'], $persona->id), [
        'stato_id' => $separato->id,
        'data_inizio' => '1990-01-01',
    ])->assertRedirect(route('nomadelfia.persone.stato', $persona->id));

    expect($persona->statiAttivi()->pluck('stati.stato')->all())->toBe(['SAC', 'SEP']);
});

it('refuses a stato the person already has active', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Don Prete']);
    $sacerdote = Stato::perNome('sacerdote');
    $persona->assegnaStato($sacerdote, '1980-03-12');

    $this->post(action([PersonaStatoController::class, 'store'], $persona->id), [
        'stato_id' => $sacerdote->id,
        'data_inizio' => '1990-01-01',
    ])->assertSessionHasErrors(['stato_id' => 'La persona Don Prete ha già lo stato SACERDOTE.']);

    expect($persona->stati()->count())->toBe(1);
});

it('refuses a stato that does not exist', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->post(action([PersonaStatoController::class, 'store'], $persona->id), [
        'stato_id' => 999999,
        'data_inizio' => '1990-01-01',
    ])->assertSessionHasErrors(['stato_id' => 'Lo stato non esiste. Celibe, nubile, sposato, vedovo, mamma di vocazione, nomadelfa mamma, mamma nubile e celibe di elezione derivano dalla famiglia.']);

    expect($persona->stati()->count())->toBe(0);
});

it('update stato of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $stato = Stato::assegnabili()->random();
    $persona->assegnaStato($stato->id, '1990-01-01');

    $this->put(action([PersonaStatoController::class, 'update'], ['id' => $persona->id, 'idStato' => $stato->id]), [
        'data_fine' => '2000-05-01',
        'data_inizio' => '1990-01-01',
        'stato' => '0',
    ])->assertRedirect(route('nomadelfia.persone.stato', $persona->id));

    expect($persona->statiAttivi()->count())->toBe(0)
        ->and($persona->stati()->first()->pivot->data_fine)->toBe('2000-05-01');
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
