<?php

namespace Tests\Livewire;

use App\Livewire\SearchPersona;
use Livewire\Livewire;

it('can render succesfully the component', function () {
    Livewire::test(SearchPersona::class)->assertStatus(200);
});

it('can set the placeholder', function () {
    Livewire::test(SearchPersona::class)
        ->set('placeholder', 'baa')
        ->assertSet('placeholder', 'baa');
});

it('can search persone', function () {
    Livewire::test(SearchPersona::class)
        ->call('search', 'Altr') // search for "Altro Cliente"
        ->assertSee('Altro Cliente');
});

// it('render the component on the page', function () {
//     login();

//     $this->get(route('officina.index'))
//         ->assertSeeLivewire(SearchPersona::class);
// });
