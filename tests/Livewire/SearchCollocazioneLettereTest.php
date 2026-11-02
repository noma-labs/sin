<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Biblioteca\Models\Libro;
use App\Livewire\SearchCollocazioneLettere;
use Livewire\Livewire;

it('can render succesfully the component', function (): void {
    Livewire::test(SearchCollocazioneLettere::class)->assertStatus(200);
});

it('can search collocazione', function (): void {
    Libro::factory()->physicalPlacement('AAA001')->create();
    Libro::factory()->physicalPlacement('BBB001')->create();

    Livewire::test(SearchCollocazioneLettere::class)
        ->call('search', 'AAA')
        ->assertSee('AAA')
        ->assertDontSee('BBB');
});
