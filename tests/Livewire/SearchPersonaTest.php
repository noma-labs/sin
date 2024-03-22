<?php

namespace Tests\Livewire;

use App\Livewire\SearchPersona;
use Domain\Nomadelfia\Persona\Models\Persona;
use Livewire\Livewire;

it("can render succesfully the component", function () {
        Livewire::test(SearchPersona::class)->assertStatus(200);
})->only();

it("can set the placeholder", function () {
    Livewire::test(SearchPersona::class)
    ->set('placeholder', 'baa')
    ->assertSet('placeholder', 'baa');
})->only();

it("can search persone", function () {
    Livewire::test(SearchPersona::class)
    ->call('search', 'Altr') // search for "Altro Cliente"
    ->assertSee('Altro Cliente');
})->only();

it("render the component on the page", function () {
    login();

    $this->get(route('officina.index'))
        ->assertSeeLivewire(SearchPersona::class);
});
