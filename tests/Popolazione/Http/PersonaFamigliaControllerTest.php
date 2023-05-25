<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Famiglia\Controllers\PersonaFamigliaController;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;

it('can render the famiglia index page of a person', function () {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaFamigliaController::class, 'index'], ['idPersona' => $persona->id]))
        ->assertSuccessful();
});

it('can create family and assign a person to it', function () {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $data_entrata = Carbon::now()->addDay()->toDatestring();
    $this->post(action([PersonaFamigliaController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'nome' => 'a-random-name',
            'posizione_famiglia' => Posizione::perNome('postulante'),
            'data_creazione' => $data_entrata,

        ])
        ->assertRedirect(route('nomadelfia.persone.famiglie', ['idPersona' => $persona->id]));
});
