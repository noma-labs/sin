<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Biblioteca\Models\Libro;
use App\Livewire\SearchCollocazioneNumeri;
use Livewire\Livewire;

it('sets the collocazione to the selected lettere when no numero is chosen', function (): void {
    Libro::factory()->physicalPlacement('AAA001')->create();

    Livewire::test(SearchCollocazioneNumeri::class, ['name' => 'xCollocazione', 'allowLettereOnly' => true])
        ->dispatch('option-selected', 'AAA')
        ->assertSet('collocazione', 'AAA')
        ->assertSeeHtml('name="xCollocazione" value="AAA"');
});

it('sets the full collocazione when a numero is chosen', function (): void {
    Libro::factory()->physicalPlacement('AAA001')->create();

    Livewire::test(SearchCollocazioneNumeri::class, ['name' => 'xCollocazione', 'allowLettereOnly' => true])
        ->dispatch('option-selected', 'AAA')
        ->set('numero', '7')
        ->assertSet('collocazione', 'AAA007');
});

it('resets the collocazione to the lettere when the numero is unselected', function (): void {
    Libro::factory()->physicalPlacement('AAA001')->create();

    Livewire::test(SearchCollocazioneNumeri::class, ['name' => 'xCollocazione', 'allowLettereOnly' => true])
        ->dispatch('option-selected', 'AAA')
        ->set('numero', '7')
        ->set('numero', '0')
        ->assertSet('collocazione', 'AAA');
});

it('keeps the collocazione empty when only the lettere are selected and lettere only is not allowed', function (): void {
    Libro::factory()->physicalPlacement('AAA001')->create();

    Livewire::test(SearchCollocazioneNumeri::class, ['name' => 'xCollocazione'])
        ->dispatch('option-selected', 'AAA')
        ->assertSet('collocazione', '');
});
