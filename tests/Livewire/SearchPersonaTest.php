<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Livewire\SearchPersona;
use Livewire\Livewire;

it('can render succesfully the component', function (): void {
    Livewire::test(SearchPersona::class)->assertStatus(200);
});

it('can set the placeholder', function (): void {
    Livewire::test(SearchPersona::class)
        ->set('placeholder', 'baa')
        ->assertSet('placeholder', 'baa');
});

it('can search persone', function (): void {
    Livewire::test(SearchPersona::class)
        ->call('search', 'Altr') // search for "Altro Cliente"
        ->assertSee('Altro Cliente');
});
