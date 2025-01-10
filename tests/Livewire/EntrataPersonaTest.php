<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Livewire\EntrataPersona;
use Livewire\Livewire;

it('can render succesfully the component', function (): void {
    Livewire::test(EntrataPersona::class)
        ->assertStatus(200);
});

it('show famiglia input if entrata is dalla_nascita', function (): void {
    Livewire::test(EntrataPersona::class)
        ->set('tipologia', 'dalla_nascita')
        ->assertSee('Famiglia')
        ->assertDontSee('Gruppo Familiare');
});

it('show famiglia input if entrata is minorenne_famiglia', function (): void {
    Livewire::test(EntrataPersona::class)
        ->set('tipologia', 'dalla_nascita')
        ->assertSee('Famiglia')
        ->assertDontSee('Gruppo Familiare');
});

it('show GruppoFamiliare input if entrata is maggiorenne_single', function (): void {
    Livewire::test(EntrataPersona::class)
        ->set('tipologia', 'maggiorenne_single')
        ->assertDontSee('Famiglia')
        ->assertSee('Gruppo Familiare');
});
