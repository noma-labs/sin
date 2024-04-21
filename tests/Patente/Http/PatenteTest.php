<?php

namespace Tests\Http\Patente;

use App\Patente\Controllers\PatenteController;

it('forbids access to guests', function () {
    $this->get(action([PatenteController::class, 'scadenze']))
        ->assertRedirect(route('login'));
});

it('shows patenti to logged users', function () {
    login();
    $this->get(action([PatenteController::class, 'scadenze']))
        ->assertOk();
});
