<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Azienda\Controllers\PersonaAziendeController;
use App\Nomadelfia\Famiglia\Controllers\PersonaFamigliaController;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;

it('can render the aziende index page of a person', function () {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaAziendeController::class, 'index'], ['idPersona' => $persona->id]))
        ->assertSuccessful();
});
