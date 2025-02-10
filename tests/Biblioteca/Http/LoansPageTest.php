<?php

declare(strict_types=1);

namespace Tests\Biblioteca\Feature;

use App\Biblioteca\Controllers\LoansController;

it('shows loans page to logged user', function (): void {
    login();

    $this
        ->get(action([LoansController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('Gestione prestiti');
});
