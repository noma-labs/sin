<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersoneController;
use Domain\Nomadelfia\Persona\Models\Persona;

it('shows form to insert a persona', function (): void {
    login();
    $this->get(action([PersoneController::class, 'create']))
        ->assertSuccessful();
});

it('render the show of a person with minimal info', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    login();

    $this->get(action([PersoneController::class, 'show'], ['idPersona' => $persona->id]))
        ->assertOk()
        ->assertSeeText($persona->nominativo);
})->only();
