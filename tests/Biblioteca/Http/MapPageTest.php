<?php

declare(strict_types=1);

namespace Tests\Biblioteca\Feature;

use App\Livewire\MappaBiblioteca;

it('shows the map of the biblioteca to a guest', function (): void {
    $this
        ->get(route('books.map'))
        ->assertOk()
        ->assertSee('Mappa della biblioteca')
        ->assertSeeLivewire(MappaBiblioteca::class);
});
