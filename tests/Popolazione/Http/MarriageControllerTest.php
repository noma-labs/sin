<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Controllers\MarriageController;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;

it('marries two people who are not part of the popolazione', function (): void {
    login();
    $husband = Persona::factory()->maggiorenne()->maschio()->create();
    $wife = Persona::factory()->maggiorenne()->femmina()->create();

    $this->post(action([MarriageController::class, 'store']), [
        'husband' => $husband->id,
        'wife' => $wife->id,
        'data_matrimonio' => Carbon::now()->toDateString(),
    ])->assertSessionHasNoErrors();

    $famiglia = $husband->famigliaAttuale();
    expect($famiglia)->toBeInstanceOf(Famiglia::class)
        ->and($famiglia->capofamiglia()->id)->toBe($husband->id)
        ->and($famiglia->moglie()->id)->toBe($wife->id);
});

it('refuses a husband who cannot marry', function (Persona $husband): void {
    login();
    $wife = Persona::factory()->maggiorenne()->femmina()->create();

    $this->post(action([MarriageController::class, 'store']), [
        'husband' => $husband->id,
        'wife' => $wife->id,
        'data_matrimonio' => Carbon::now()->toDateString(),
    ])->assertSessionHasErrors(['husband' => 'Lo sposo deve essere un uomo maggiorenne, vivo e non già sposato.']);

    expect($wife->famigliaAttuale())->toBeNull();
})->with([
    'deceased' => fn (): Persona => Persona::factory()->maggiorenne()->maschio()->create(['data_decesso' => '2020-01-01']),
    'minor' => fn (): Persona => Persona::factory()->minorenne()->maschio()->create(),
    'woman' => fn (): Persona => Persona::factory()->maggiorenne()->femmina()->create(),
    'already married' => function (): Persona {
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        Famiglia::factory()->create()->assegnaCapoFamiglia($persona);

        return $persona;
    },
]);

it('refuses a wife who cannot marry', function (): void {
    login();
    $husband = Persona::factory()->maggiorenne()->maschio()->create();
    $wife = Persona::factory()->maggiorenne()->femmina()->create(['data_decesso' => '2020-01-01']);

    $this->post(action([MarriageController::class, 'store']), [
        'husband' => $husband->id,
        'wife' => $wife->id,
        'data_matrimonio' => Carbon::now()->toDateString(),
    ])->assertSessionHasErrors(['wife' => 'La sposa deve essere una donna maggiorenne, viva e non già sposata.']);
});

it('renders the matrimonio form', function (): void {
    login();

    $this->get(action([MarriageController::class, 'create']))
        ->assertSuccessful()
        ->assertSeeLivewire('search-persona');
});

it('preselects the person the matrimonio starts from as husband or wife', function (): void {
    login();
    $wife = Persona::factory()->maggiorenne()->femmina()->create();

    $this->get(action([MarriageController::class, 'create'], ['persona' => $wife->id]))
        ->assertSuccessful()
        ->assertViewHas('wifeId', [$wife->id])
        ->assertViewHas('husbandId', []);
});
