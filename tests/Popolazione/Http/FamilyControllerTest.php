<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Controllers\FamilyController;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Carbon\Carbon;

it('lists the families of people who are not part of the popolazione', function (): void {
    login();
    $capofamiglia = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Fuori Nomadelfia']);
    $famiglia->assegnaCapoFamiglia($capofamiglia);

    $this->get(action([FamilyController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('Famiglia Fuori Nomadelfia');
});

it('lists only the single people in Nomadelfia unless all are requested', function (): void {
    login();
    $interno = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Single Interno']);
    app(EntrataMaggiorenneSingleAction::class)->execute($interno, Carbon::now(), GruppoFamiliare::all()->random());
    Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Single Esterno']);

    $this->get(action([FamilyController::class, 'index']))
        ->assertSee('Single Interno')
        ->assertDontSee('Single Esterno');

    $this->get(action([FamilyController::class, 'index'], ['single' => 'tutti']))
        ->assertSee('Single Interno')
        ->assertSee('Single Esterno');
});

it('redirects the old nomadelfia urls to the anagrafe', function (string $oldUrl, string $newUrl): void {
    $this->get($oldUrl)->assertRedirect($newUrl)->assertStatus(301);
})->with([
    'families' => ['/nomadelfia/families', '/anagrafe/families'],
    'family' => ['/nomadelfia/families/7', '/anagrafe/families/7'],
    'search' => ['/nomadelfia/search', '/anagrafe/search'],
]);

it('shows a family whose members are not part of the popolazione', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Da Mostrare']);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());

    $this->get(action([FamilyController::class, 'show'], $famiglia->id))
        ->assertSuccessful()
        ->assertSee('Famiglia Da Mostrare');
});

it('does not list deceased single people', function (): void {
    login();
    Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Single Deceduto', 'data_decesso' => '2020-01-01']);

    $this->get(action([FamilyController::class, 'index'], ['single' => 'tutti']))
        ->assertSuccessful()
        ->assertDontSee('Single Deceduto');
});

it('saves a famiglia without data_creazione', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Senza Data', 'data_creazione' => '1998-06-12']);

    $this->put(action([FamilyController::class, 'update'], $famiglia->id), [
        'nome_famiglia' => 'Famiglia Senza Data',
        'data_creazione' => '',
    ])->assertRedirect(route('anagrafe.families.show', $famiglia->id));

    expect(Famiglia::findOrFail($famiglia->id)->data_creazione)->toBeNull();
});

it('changes the tipo of a famiglia', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Di Vocazione', 'data_creazione' => '1980-03-12']);

    $this->put(action([FamilyController::class, 'update'], $famiglia->id), [
        'nome_famiglia' => 'Famiglia Di Vocazione',
        'tipo' => TipoFamiglia::MammaDiVocazione->value,
        'data_creazione' => '1980-03-12',
    ])->assertRedirect(route('anagrafe.families.show', $famiglia->id));

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBe(TipoFamiglia::MammaDiVocazione);
    $this->get(action([FamilyController::class, 'show'], $famiglia->id))->assertSee('Mamma di vocazione');
});

it('refuses a tipo of famiglia that does not exist', function (): void {
    login();
    $famiglia = Famiglia::factory()->create();

    $this->put(action([FamilyController::class, 'update'], $famiglia->id), [
        'nome_famiglia' => $famiglia->nome_famiglia,
        'tipo' => 'SINGLE',
        'data_creazione' => $famiglia->data_creazione,
    ])->assertSessionHasErrors(['tipo' => 'Il tipo della famiglia non esiste.']);

    expect(Famiglia::findOrFail($famiglia->id)->tipo)->toBeNull();
});
