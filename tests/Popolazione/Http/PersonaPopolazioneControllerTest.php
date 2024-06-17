<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PersonaPopolazioneController;
use Domain\Nomadelfia\Persona\Models\Persona;

it('can render the popolazione history of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaPopolazioneController::class, 'index'], ['idPersona' => $persona->id]))
        ->assertSuccessful();
});
