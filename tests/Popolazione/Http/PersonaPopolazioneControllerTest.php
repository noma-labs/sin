<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Famiglia\Controllers\PersonaFamigliaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PersonaPopolazioneController;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;

it('can render the popolazione history of a person', function () {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaPopolazioneController::class, 'index'], ['idPersona' => $persona->id]))
        ->assertSuccessful();
});
