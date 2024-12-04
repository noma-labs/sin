<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersoneController;

it('shows form to insert a persona', function (): void {
    login();
    $this->get(action([PersoneController::class, 'create']))
        ->assertSuccessful();
});
