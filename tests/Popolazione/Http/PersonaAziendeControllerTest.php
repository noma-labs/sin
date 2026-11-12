<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\Azienda\Controllers\PersonaAziendeController;
use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;
use Carbon\Carbon;

it('can render the aziende index page of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaAziendeController::class, 'index'], ['idPersona' => $persona->id]))
        ->assertSuccessful();
});

it('can assign a person to a azienda', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $data_entrata = Carbon::now()->addDay()->toDatestring();
    $this->post(action([PersonaAziendeController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'azienda_id' => Azienda::all()->random()->id,
            'mansione' => 'LAVORATORE',
            'data_inizio' => $data_entrata,

        ])
        ->assertRedirect(route('nomadelfia.persone.aziende', ['idPersona' => $persona->id]));
});

it('assigns an azienda from the profile and goes back to it', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $azienda = Azienda::factory()->create();

    $this->from(route('nomadelfia.person.profile', $persona->id))
        ->post(route('nomadelfia.persone.aziende.assegna', ['idPersona' => $persona->id]), [
            'azienda_id' => $azienda->id,
            'mansione' => Azienda::MANSIONE_RESPONSABILE,
            'data_inizio' => '2024-02-01',
            'torna_indietro' => '1',
        ])
        ->assertRedirect(route('nomadelfia.person.profile', $persona->id));

    expect($persona->aziendeAttuali()->sole()->pivot->toArray())
        ->toMatchArray(['azienda_id' => $azienda->id, 'mansione' => Azienda::MANSIONE_RESPONSABILE, 'data_inizio_azienda' => '2024-02-01']);
});

it('can edit an azienda of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $data_inizio = Carbon::now()->toDatestring();
    $azienda = Azienda::all()->random();

    $action = new AssegnaAziendaAction;
    $action->execute($persona, $azienda, Carbon::parse($data_inizio), Azienda::MANSIONE_LAVORATORE);

    $this->post(action([PersonaAziendeController::class, 'update'], ['idPersona' => $persona->id, 'id' => $azienda->id]),
        [
            'mansione' => 'LAVORATORE',
            'data_entrata' => $data_inizio,
            'stato' => 1,
        ])
        ->assertRedirect(route('nomadelfia.persone.aziende', ['idPersona' => $persona->id]));
});
