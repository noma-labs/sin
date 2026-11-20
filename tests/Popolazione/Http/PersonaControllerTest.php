<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Persona\Controllers\PersonController;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Carbon\Carbon;
use Illuminate\Support\Str;

it('shows form to insert a persona', function (): void {
    login();
    $this->get(action([PersonController::class, 'create']))
        ->assertSuccessful();
});

it('render the show of a person with minimal info', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    login();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertOk()
        ->assertSeeText($persona->nominativo);
});

it('lists all people in the anagrafe, marking who is in Nomadelfia', function (): void {
    login();
    $interno = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Aaa Persona Interna']);
    app(EntrataMaggiorenneSingleAction::class)->execute($interno, Carbon::now(), GruppoFamiliare::all()->random());
    Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Aaa Persona Esterna', 'data_decesso' => '2020-01-01']);

    $this->get(action([PersonController::class, 'index']))
        ->assertSuccessful()
        ->assertSeeInOrder(['Aaa Persona Esterna', 'Deceduto', 'Aaa Persona Interna', 'In Nomadelfia'])
        ->assertSee(route('anagrafe.person.create'));
});

it('offers a nuova famiglia of the tipi of her sesso from the page of an unmarried adult', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();

    $this->get(route('nomadelfia.person.profile', $persona->id))
        ->assertSee(route('anagrafe.families.create', ['persona' => $persona->id, 'tipo' => 'CON']))
        ->assertSee(route('anagrafe.families.create', ['persona' => $persona->id, 'tipo' => 'MAV']))
        ->assertDontSee(route('anagrafe.families.create', ['persona' => $persona->id, 'tipo' => 'CDE']));
});

it('requires authentication for the anagrafe search page', function (): void {
    $this->get(route('anagrafe.people.search'))->assertRedirect(route('login'));
});

it('searches people by nominativo, nome, cognome or numero elenco with a single text', function (string $field): void {
    $persona = Persona::factory()->create([
        'nominativo' => 'Nominativo'.Str::random(10),
        'nome' => 'Nome'.Str::random(10),
        'cognome' => 'Cognome'.Str::random(10),
        'numero_elenco' => 'Z'.fake()->unique()->numberBetween(10000, 99999),
    ]);
    $other = Persona::factory()->create(['nominativo' => 'Altro'.Str::random(10)]);

    login();

    $this->get(route('anagrafe.people.search.show', ['q' => $persona->{$field}]))
        ->assertSuccessful()
        ->assertSee($persona->nominativo)
        ->assertDontSee($other->nominativo);
})->with(['nominativo', 'nome', 'cognome', 'numero_elenco']);

it('shows the persone found with their avatar and a link to their profile', function (): void {
    $cognome = 'Ricercato'.Str::lower(Str::random(8));
    $conFoto = Persona::factory()->maschio()->create(['nome' => 'Elio', 'cognome' => $cognome, 'nominativo' => 'Elietto', 'avatar_path' => 'persone/elio.jpg']);
    $senzaFoto = Persona::factory()->femmina()->create(['nome' => 'Gaia', 'cognome' => $cognome, 'nominativo' => "Gaia {$cognome}"]);

    login();

    $this->get(route('anagrafe.people.search.show', ['q' => $cognome]))
        ->assertSuccessful()
        ->assertSee(route('nomadelfia.person.profile', ['id' => $conFoto->id]), false)
        ->assertSee('<img src="'.e($conFoto->avatarUrl()).'"', false)
        ->assertSee('(Elietto)')
        ->assertSee(route('nomadelfia.person.profile', ['id' => $senzaFoto->id]), false)
        ->assertSee('persona-avatar persona-avatar-sm persona-avatar-f', false);
});

it('shows the person search box in the popolazione navbar', function (): void {
    login();

    $this->get(route('nomadelfia.index'))
        ->assertSuccessful()
        ->assertSee('name="q"', false);
});
