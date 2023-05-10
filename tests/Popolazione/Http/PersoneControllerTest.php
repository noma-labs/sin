<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaAnagraficaController;
use App\Nomadelfia\Persona\Controllers\PersoneController;
use Domain\Nomadelfia\Persona\Models\Persona;


it('shows form to insert a persona', function () {
    login();
    $this->get(action([PersoneController::class, 'create']))
        ->assertSuccessful();
});
