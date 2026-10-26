<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Controllers\MarriageController;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
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
        ->and($famiglia->tipo)->toBe(TipoFamiglia::Coniugi)
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

it('refuses a spouse that is not a person id', function (): void {
    login();
    $wife = Persona::factory()->maggiorenne()->femmina()->create();

    $this->post(action([MarriageController::class, 'store']), [
        'husband' => ['1'],
        'wife' => $wife->id,
        'data_matrimonio' => Carbon::now()->toDateString(),
    ])->assertSessionHasErrors('husband');
});

it('keeps the spouses selected before a validation error', function (): void {
    login();
    $husband = Persona::factory()->maggiorenne()->maschio()->create();
    $wife = Persona::factory()->maggiorenne()->femmina()->create();

    $this->withSession(['_old_input' => ['husband' => (string) $husband->id, 'wife' => (string) $wife->id]])
        ->get(action([MarriageController::class, 'create']))
        ->assertViewHas('husbandId', [$husband->id])
        ->assertViewHas('wifeId', [$wife->id]);
});

it('moves the spouses in Nomadelfia into the gruppo chosen for the famiglia', function (): void {
    login();
    [$vecchioGruppo, $nuovoGruppo] = GruppoFamiliare::query()->take(2)->get();
    $husband = Persona::factory()->maggiorenne()->maschio()->create();
    $wife = Persona::factory()->maggiorenne()->femmina()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($husband, Carbon::parse('2020-01-01'), $vecchioGruppo);
    app(EntrataMaggiorenneSingleAction::class)->execute($wife, Carbon::parse('2020-01-01'), $nuovoGruppo);

    $this->post(action([MarriageController::class, 'store']), [
        'husband' => $husband->id,
        'wife' => $wife->id,
        'data_matrimonio' => '2022-06-12',
        'gruppo_id' => $nuovoGruppo->id,
    ])->assertSessionHasNoErrors();

    expect($husband->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id)
        ->and($husband->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe('2022-06-12')
        ->and($husband->gruppofamiliariStorico()->first()->pivot->data_uscita_gruppo)->toBe('2022-06-12')
        ->and($wife->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id)
        ->and($wife->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe('2020-01-01')
        ->and($husband->famigliaAttuale()->gruppoFamiliareAttuale()->id)->toBe($nuovoGruppo->id);
});

it('moves a spouse married before entering into the gruppo from the entrata', function (): void {
    login();
    [$vecchioGruppo, $nuovoGruppo] = GruppoFamiliare::query()->take(2)->get();
    $husband = Persona::factory()->maggiorenne()->maschio()->create();
    $wife = Persona::factory()->maggiorenne()->femmina()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($husband, Carbon::parse('2020-01-01'), $vecchioGruppo);

    $this->post(action([MarriageController::class, 'store']), [
        'husband' => $husband->id,
        'wife' => $wife->id,
        'data_matrimonio' => '1998-06-12',
        'gruppo_id' => $nuovoGruppo->id,
    ])->assertSessionHasNoErrors();

    expect($husband->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id)
        ->and($husband->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe('2020-01-01')
        ->and($wife->gruppofamiliareAttuale())->toBeNull();
});

it('requires the gruppo when a spouse is in Nomadelfia', function (): void {
    login();
    $husband = Persona::factory()->maggiorenne()->maschio()->create();
    $wife = Persona::factory()->maggiorenne()->femmina()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($wife, Carbon::parse('2020-01-01'), GruppoFamiliare::first());

    $this->post(action([MarriageController::class, 'store']), [
        'husband' => $husband->id,
        'wife' => $wife->id,
        'data_matrimonio' => '2022-06-12',
    ])->assertSessionHasErrors(['gruppo_id' => 'Il gruppo familiare è obbligatorio quando uno degli sposi è in Nomadelfia.']);

    expect($husband->famigliaAttuale())->toBeNull()
        ->and($wife->gruppofamiliareAttuale()->id)->toBe(GruppoFamiliare::first()->id);
});
