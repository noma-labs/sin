<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersoneController;

it('shows form to insert a persona', function () {
    login();
    $this->get(action([PersoneController::class, 'create']))
        ->assertSuccessful();
});
