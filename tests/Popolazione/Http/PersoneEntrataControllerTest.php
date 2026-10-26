<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Controllers\MarriageController;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Controllers\JoinCommunityController;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Carbon\Carbon;

it('can show the form to enter a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([JoinCommunityController::class, 'create'], $persona->id))
        ->assertSuccessful();
});

it('it_can_insert_minorenne_accolto_nella_popolazione', function (): void {
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $data_entrata = Carbon::now()->startOfDay();
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, $data_entrata, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam);

    login();
    $this->withoutExceptionHandling();
    $this->post(action([JoinCommunityController::class, 'store'], $persona->id),
        [
            'tipologia' => 'minorenne_accolto',
            'data_entrata' => $data_entrata->toDatestring(),
            'famiglia_id' => $famiglia->id,
        ]);
    //            ->assertSee("inserita correttamente.");Controllers

    $persona = Persona::findOrFail($persona->id);
    $figlio = Posizione::perNome('figlio');
    $this->assertTrue($persona->isPersonaInterna());
    $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
    $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
    $this->assertEquals(Carbon::parse($persona->posizioneAttuale()->pivot->data_inizio), $data_entrata);
    $this->assertEquals('Celibe', $persona->statoCivile()['nome']);
    $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
    $this->assertEquals(Carbon::parse($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo), $data_entrata);
    $this->assertNotNull($persona->famigliaAttuale());
    $this->assertEquals($persona->famigliaAttuale()->pivot->posizione_famiglia, Famiglia::getFiglioAccoltoEnum());
});

it('it_can_insert_minorenne_con_famiglia_nella_popolazione', function (): void {
    $data_nascita = Carbon::now()->startOfDay();
    $persona = Persona::factory()->minorenne()->nato($data_nascita)->maschio()->create();
    $data_entrata = Carbon::now()->startOfDay();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, $data_entrata, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam);

    login();
    $this->post(action([JoinCommunityController::class, 'store'], $persona->id),
        [
            'tipologia' => 'minorenne_famiglia',
            'data_entrata' => $data_entrata,
            'famiglia_id' => $famiglia->id,
        ]);

    $this->assertTrue($persona->isPersonaInterna());
    $figlio = Posizione::perNome('figlio');
    $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
    $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
    $this->assertEquals(Carbon::parse($persona->posizioneAttuale()->pivot->data_inizio), $data_entrata);
    $this->assertEquals('Celibe', $persona->statoCivile()['nome']);
    $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
    $this->assertEquals(Carbon::parse($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo), $data_entrata);
    $this->assertNotNull($persona->famigliaAttuale());
    $this->assertEquals($persona->famigliaAttuale()->pivot->posizione_famiglia, Famiglia::getFiglioNatoEnum());
});

it('entrata_persona_dalla_nascita', function (): void {
    $data_nascita = Carbon::now()->startOfDay();
    $persona = Persona::factory()->minorenne()->nato($data_nascita)->femmina()->create();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, Carbon::now(), $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam);

    login();
    $this->withoutExceptionHandling();
    $this->post(action([JoinCommunityController::class, 'store'], $persona->id),
        [
            'tipologia' => 'dalla_nascita',
            'famiglia_id' => $famiglia->id,
        ]);

    $this->assertTrue($persona->isPersonaInterna());
    $this->assertEquals(Carbon::parse($persona->getDataEntrataNomadelfia()), $data_nascita);
    $figlio = Posizione::perNome('figlio');
    $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
    $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_nascita->toDatestring());
    $this->assertEquals('Nubile', $persona->statoCivile()['nome']);
    $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
    $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_nascita->toDatestring());
    $this->assertNotNull($persona->famigliaAttuale());
    $this->assertEquals($persona->famigliaAttuale()->pivot->posizione_famiglia, Famiglia::getFiglioNatoEnum());
});

it('entrata_maggiorenne_single', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $ospite = Posizione::perNome('ospite');

    login();

    $this->post(action([JoinCommunityController::class, 'store'], $persona->id),
        [
            'tipologia' => 'maggiorenne_single',
            'gruppo_id' => $gruppo->id,
            'data_entrata' => $data_entrata->toDateString(),
        ]);

    expect($persona->isPersonaInterna())->toBeTrue()
        ->and($persona->getDataEntrataNomadelfia())->toEqual($data_entrata)
        ->and($persona->posizioneAttuale()->id)->toEqual($ospite->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toEqual($data_entrata->toDateString())
        ->and($persona->statoCivile())->toEqual(['nome' => 'Celibe', 'dal' => Carbon::parse($persona->data_nascita)])
        ->and($persona->gruppofamiliareAttuale()->id)->toEqual($gruppo->id)
        ->and($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toEqual($data_entrata->toDateString())
        ->and($persona->famigliaAttuale())->toBeNull();
});

it('entrata_maggiorenne_sposato', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->create();
    $gruppo = GruppoFamiliare::first();
    $ospite = Posizione::perNome('ospite');

    login();
    $this->post(action([JoinCommunityController::class, 'store'], $persona->id),
        [
            'tipologia' => 'maggiorenne_famiglia',
            'gruppo_id' => $gruppo->id,
            'data_entrata' => $data_entrata->toDatestring(),
        ]);

    $this->assertTrue($persona->isPersonaInterna());
    $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
    $this->assertEquals($persona->posizioneAttuale()->id, $ospite->id);
    $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata->toDateString());
    $this->assertCount(0, $persona->statiAttivi()->get());
    $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
    $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata->toDateString());
    $this->assertNull($persona->famigliaAttuale());
});

it('enters a famiglia: the spouses, then the matrimonio with the gruppo, then the figli', function (): void {
    login();
    $gruppo = GruppoFamiliare::first();
    $marito = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $figlio = Persona::factory()->minorenne()->maschio()->create();
    $data_entrata = Carbon::now()->startOfDay();

    foreach ([$marito, $moglie] as $sposo) {
        $this->post(action([JoinCommunityController::class, 'store'], $sposo->id), [
            'tipologia' => 'maggiorenne_famiglia',
            'gruppo_id' => $gruppo->id,
            'data_entrata' => $data_entrata->toDateString(),
        ])->assertSessionHasNoErrors();
    }
    $this->post(action([MarriageController::class, 'store']), [
        'husband' => $marito->id,
        'wife' => $moglie->id,
        'data_matrimonio' => '2010-05-01',
        'gruppo_id' => $gruppo->id,
    ])->assertSessionHasNoErrors();
    $famiglia = $marito->famigliaAttuale();

    $this->post(action([JoinCommunityController::class, 'store'], $figlio->id), [
        'tipologia' => 'minorenne_famiglia',
        'famiglia_id' => $famiglia->id,
        'data_entrata' => $data_entrata->toDateString(),
    ])->assertSessionHasNoErrors();

    expect($marito->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($moglie->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($marito->statoCivile()['nome'])->toBe('Sposato')
        ->and($figlio->isPersonaInterna())->toBeTrue()
        ->and($figlio->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($figlio->famigliaAttuale()->id)->toBe($famiglia->id)
        ->and($figlio->famigliaAttuale()->pivot->posizione_famiglia)->toBe(Famiglia::getFiglioNatoEnum());
});

it('shows an error instead of failing when the famiglia has no gruppo familiare', function (): void {
    login();
    $famiglia = Famiglia::factory()->create(['nome_famiglia' => 'Famiglia Senza Gruppo']);
    $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());
    $figlio = Persona::factory()->minorenne()->maschio()->create();

    $this->post(action([JoinCommunityController::class, 'store'], $figlio->id), [
        'tipologia' => 'minorenne_famiglia',
        'famiglia_id' => $famiglia->id,
        'data_entrata' => Carbon::now()->toDateString(),
    ])->assertSessionHasErrors('tipologia');

    expect($figlio->isPersonaInterna())->toBeFalse();
});

it('refuses to enter as minorenne a person who is maggiorenne at the entrata', function (string $tipologia): void {
    login();
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneConFamigliaAction::class)->execute($capoFam, Carbon::now()->startOfDay(), GruppoFamiliare::first());
    $famiglia->assegnaCapoFamiglia($capoFam);
    $adulto = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'Adulto Accolto']);

    $this->post(action([JoinCommunityController::class, 'store'], $adulto->id), [
        'tipologia' => $tipologia,
        'famiglia_id' => $famiglia->id,
        'data_entrata' => Carbon::now()->toDateString(),
    ])->assertSessionHasErrors(['tipologia' => 'La persona `Adulto Accolto` è maggiorenne.']);

    expect($adulto->isPersonaInterna())->toBeFalse();
})->with(['minorenne_accolto', 'minorenne_famiglia']);

it('enters as minorenne an adult who was minorenne at the entrata', function (): void {
    login();
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneConFamigliaAction::class)->execute($capoFam, Carbon::parse('1990-01-01'), GruppoFamiliare::first());
    $famiglia->assegnaCapoFamiglia($capoFam);
    $accolto = Persona::factory()->nato(Carbon::parse('1985-03-01'))->maschio()->create();

    $this->post(action([JoinCommunityController::class, 'store'], $accolto->id), [
        'tipologia' => 'minorenne_accolto',
        'famiglia_id' => $famiglia->id,
        'data_entrata' => '1995-09-01',
    ])->assertSessionHasNoErrors();

    expect($accolto->isPersonaInterna())->toBeTrue();
});

it('refuses a famiglia or a gruppo that does not exist', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();

    $this->post(action([JoinCommunityController::class, 'store'], $persona->id), [
        'tipologia' => 'minorenne_accolto',
        'famiglia_id' => 999999,
        'data_entrata' => Carbon::now()->toDateString(),
    ])->assertSessionHasErrors(['famiglia_id' => 'La famiglia selezionata non esiste.']);

    $this->post(action([JoinCommunityController::class, 'store'], $persona->id), [
        'tipologia' => 'maggiorenne_single',
        'gruppo_id' => 999999,
        'data_entrata' => Carbon::now()->toDateString(),
    ])->assertSessionHasErrors(['gruppo_id' => 'Il gruppo familiare selezionato non esiste.']);
});
