<?php

declare(strict_types=1);

namespace Tests\Http\Patente;

use App\Patente\Controllers\PatenteController;

it('forbids access to guests', function (): void {
    $this->get(action([PatenteController::class, 'index']))
        ->assertRedirect(route('login'));
});

it('shows patenti to logged users', function (): void {
    login();
    $this->get(action([PatenteController::class, 'index']))
        ->assertOk();
});
