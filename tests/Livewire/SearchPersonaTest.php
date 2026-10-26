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

it('asks for at least 3 characters', function (): void {
    Livewire::test(SearchPersona::class)
        ->set('searchTerm', 'ab')
        ->assertSet('options', [])
        ->assertSee('-- Inserisci almeno 3 caratteri --');
});

it('treats like wildcards in the term as literal characters', function (): void {
    Persona::factory()->create(['nominativo' => 'wildcard']);

    Livewire::test(SearchPersona::class)
        ->call('search', 'wil%')
        ->assertDontSee('Wildcard');
});

it('shows the selected persone with the same label as the search', function (): void {
    $persona = Persona::factory()->create(['nominativo' => 'etichetta', 'nome' => 'Mario', 'cognome' => 'Rossi', 'data_nascita' => '1975-03-02']);

    Livewire::test(SearchPersona::class, ['persone_id' => $persona->id])
        ->assertSee('(1975) Mario  Rossi (Etichetta)');
});

it('selects and deselects multiple persone', function (): void {
    $first = Persona::factory()->create(['nominativo' => 'multi-uno']);
    $second = Persona::factory()->create(['nominativo' => 'multi-due']);

    $component = Livewire::test(SearchPersona::class, ['multiple' => true])
        ->call('search', 'multi')
        ->call('select', $first->id)
        ->call('search', 'multi')
        ->call('select', $second->id)
        ->call('search', 'multi')
        ->call('select', $first->id);

    expect(collect($component->get('selected'))->pluck('id')->all())->toEqualCanonicalizing([$first->id, $second->id]);

    $component->call('deselect', $first->id);

    expect(collect($component->get('selected'))->pluck('id')->all())->toBe([$second->id]);
});

it('replaces the selected persona in single mode', function (): void {
    $first = Persona::factory()->create(['nominativo' => 'singolo-uno']);
    $second = Persona::factory()->create(['nominativo' => 'singolo-due']);

    $component = Livewire::test(SearchPersona::class, ['persone_id' => $first->id])
        ->call('search', 'singolo')
        ->call('select', $second->id);

    expect(collect($component->get('selected'))->pluck('id')->all())->toBe([$second->id]);
});

it('ignores the selection of an option not in the results', function (): void {
    $persona = Persona::factory()->create();

    Livewire::test(SearchPersona::class)
        ->call('select', $persona->id)
        ->assertSet('selected', [])
        ->assertNotDispatched('option-selected');
});
