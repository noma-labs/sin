<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Anagrafe\Persona\Models\Persona;
use App\Livewire\SearchPersona;
use Illuminate\Support\Facades\DB;
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

it('can dispatch event', function (): void {
    $p = Persona::factory()->create(['nominativo' => 'a-person']);

    Livewire::test(SearchPersona::class)
        ->call('search', 'a-person')
        ->call('select', $p->id)
        ->assertDispatched('option-selected');
});

it('shows the dates of the latest stay of a persona', function (): void {
    $persona = Persona::factory()->create(['nominativo' => 'rientrato']);
    DB::connection('db_nomadelfia')->table('popolazione')->insert([
        ['persona_id' => $persona->id, 'data_entrata' => '1980-01-01', 'data_uscita' => '1990-01-01'],
        ['persona_id' => $persona->id, 'data_entrata' => '2000-01-01', 'data_uscita' => null],
    ]);

    Livewire::test(SearchPersona::class)
        ->call('search', 'rientrato')
        ->assertSee('2000-01-01')
        ->assertDontSee('1990-01-01')
        ->assertDontSee('1980-01-01');
});

it('ignores the selection of an option not in the results', function (): void {
    $persona = Persona::factory()->create();

    Livewire::test(SearchPersona::class)
        ->call('select', $persona->id)
        ->assertSet('selected', [])
        ->assertNotDispatched('option-selected');
});
