<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Controllers\FamilyController;
use App\Anagrafe\Famiglia\Models\Famiglia;
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
    'person' => ['/nomadelfia/people/42', '/anagrafe/people/42'],
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
