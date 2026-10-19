<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Persona\Controllers\PersonController;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Controllers\PersonaPopolazioneController;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Carbon\Carbon;

it('shows form to insert a persona', function (): void {
    login();
    $this->get(action([PersonController::class, 'create']))
        ->assertSuccessful();
});

it('render the show of a person with minimal info', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    login();

    $this->get(action([PersonaPopolazioneController::class, 'show'], $persona->id))
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

it('offers a nuovo matrimonio from the page of an unmarried adult', function (): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $this->get(action([PersonaPopolazioneController::class, 'show'], $persona->id))
        ->assertSee(route('anagrafe.marriage.create', ['persona' => $persona->id]));
});

it('requires authentication for the anagrafe search page', function (): void {
    $this->get(route('anagrafe.people.search'))->assertRedirect(route('login'));
});
