<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Azienda\Controllers\PersonaAziendeController;
use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;

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
