<?php

declare(strict_types=1);

namespace Tests\Biblioteca\Feature;

use App\Biblioteca\Controllers\SearchableLibriController;

it('guest user can see homepage', function (): void {
    $this
        ->get(route('biblioteca'))
        ->assertSee('Libri');
});

it('guest user can search book page homepage', function (): void {
    $this
        ->get(action([SearchableLibriController::class, 'index']))
        ->assertSee('Ricerca Libro');
});
