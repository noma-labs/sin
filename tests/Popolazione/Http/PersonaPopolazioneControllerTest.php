<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\JoinLeaveHistoryController;

it('can render the popolazione history of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([JoinLeaveHistoryController::class, 'index'], $persona->id))
        ->assertSuccessful();
});
