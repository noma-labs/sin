<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Biblioteca\Models\Libro;
use App\Livewire\SearchCollocazioneNumeri;
use Livewire\Exceptions\EventHandlerDoesNotExist;
use Livewire\Livewire;

it('sets the collocazione to the selected lettere when no numero is chosen', function (): void {
    Libro::factory()->physicalPlacement('AAA001')->create();

    Livewire::test(SearchCollocazioneNumeri::class, ['name' => 'xCollocazione', 'allowLettereOnly' => true])
        ->dispatch(SearchCollocazioneNumeri::LETTERE_SELECTED, 'AAA')
        ->assertSet('collocazione', 'AAA')
        ->assertSeeHtml('name="xCollocazione" value="AAA"');
});

it('sets the full collocazione when a numero is chosen', function (): void {
    Libro::factory()->physicalPlacement('AAA001')->create();

    Livewire::test(SearchCollocazioneNumeri::class, ['name' => 'xCollocazione', 'allowLettereOnly' => true])
        ->dispatch(SearchCollocazioneNumeri::LETTERE_SELECTED, 'AAA')
        ->set('numero', '7')
        ->assertSet('collocazione', 'AAA007');
});

it('resets the collocazione to the lettere when the numero is unselected', function (): void {
    Libro::factory()->physicalPlacement('AAA001')->create();

    Livewire::test(SearchCollocazioneNumeri::class, ['name' => 'xCollocazione', 'allowLettereOnly' => true])
        ->dispatch(SearchCollocazioneNumeri::LETTERE_SELECTED, 'AAA')
        ->set('numero', '7')
        ->set('numero', '0')
        ->assertSet('collocazione', 'AAA');
});

it('keeps the collocazione empty when only the lettere are selected and lettere only is not allowed', function (): void {
    Libro::factory()->physicalPlacement('AAA001')->create();

    Livewire::test(SearchCollocazioneNumeri::class, ['name' => 'xCollocazione'])
        ->dispatch(SearchCollocazioneNumeri::LETTERE_SELECTED, 'AAA')
        ->assertSet('collocazione', '');
});

it('does not listen to the options selected in the other autocompletes of the page', function (): void {
    expect(fn () => Livewire::test(SearchCollocazioneNumeri::class, ['name' => 'collocazione'])
        ->dispatch('option-selected', 42))
        ->toThrow(EventHandlerDoesNotExist::class);
});
