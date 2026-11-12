<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Biblioteca\Models\Libro;
use App\Livewire\SearchCollocazioneLettere;
use App\Livewire\SearchCollocazioneNumeri;
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

it('tells the numeri which lettere are selected', function (): void {
    Libro::factory()->physicalPlacement('AAA001')->create();

    Livewire::test(SearchCollocazioneLettere::class)
        ->call('search', 'AAA')
        ->call('select', 'AAA')
        ->assertDispatched(SearchCollocazioneNumeri::LETTERE_SELECTED, 'AAA')
        ->assertNotDispatched('option-selected');
});
