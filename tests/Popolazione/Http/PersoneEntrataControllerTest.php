<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Controllers\JoinCommunityController;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
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

it('corrects the data di entrata of a period in the storico', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1960-01-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert(['persona_id' => $persona->id, 'data_entrata' => '1990-01-01', 'data_uscita' => '1995-12-31']);

    $this->put(route('nomadelfia.join.update', ['id' => $persona->id, 'entrata' => '1990-01-01']), ['data_entrata' => '1991-06-15'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->get(['data_entrata', 'data_uscita'])->toArray())
        ->toBe([['data_entrata' => '1991-06-15', 'data_uscita' => '1995-12-31']]);
});

it('rejects a data di entrata that is not a valid correction of the period', function (string $dataEntrata, string $errore): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1960-01-01'))->maschio()->create();
    PopolazioneNomadelfia::query()->insert([
        ['persona_id' => $persona->id, 'data_entrata' => '1980-01-01', 'data_uscita' => '1985-12-31'],
        ['persona_id' => $persona->id, 'data_entrata' => '1990-01-01', 'data_uscita' => '1995-12-31'],
    ]);

    $this->put(route('nomadelfia.join.update', ['id' => $persona->id, 'entrata' => '1990-01-01']), ['data_entrata' => $dataEntrata])
        ->assertSessionHasErrors(['data_entrata' => $errore]);

    expect(PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->where('data_entrata', '1990-01-01')->exists())->toBeTrue();
})->with([
    'empty' => ['', 'La data di entrata è obbligatoria.'],
    'after the data di uscita' => ['1996-01-01', 'La data di entrata non può essere futura o successiva alla data di uscita.'],
    'before the nascita' => ['1959-12-31', 'La data di entrata non può essere precedente alla data di nascita.'],
    'overlapping another period' => ['1985-06-01', 'Il periodo si sovrappone a quello dal 1980-01-01 al 1985-12-31.'],
]);

it('moves the posizione and the gruppo familiare that started with the entrata to the corrected data di entrata', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    $gruppo = GruppoFamiliare::all()->random();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2000-01-01'), $gruppo);
    app(UscitaPersonaAction::class)->execute($persona, Carbon::parse('2005-01-01'));
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2010-03-01'), $gruppo);

    $this->put(route('nomadelfia.join.update', ['id' => $persona->id, 'entrata' => '2010-03-01']), ['data_entrata' => '2010-02-01'])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('popolazione', ['persona_id' => $persona->id, 'data_entrata' => '2010-02-01', 'data_uscita' => null], 'db_nomadelfia');
    $this->assertDatabaseHas('persone_posizioni', ['persona_id' => $persona->id, 'data_inizio' => '2010-02-01', 'stato' => '1'], 'db_nomadelfia');
    $this->assertDatabaseHas('gruppi_persone', ['persona_id' => $persona->id, 'data_entrata_gruppo' => '2010-02-01', 'stato' => '1'], 'db_nomadelfia');
    $this->assertDatabaseHas('persone_posizioni', ['persona_id' => $persona->id, 'data_inizio' => '2000-01-01', 'data_fine' => '2005-01-01'], 'db_nomadelfia');
    $this->assertDatabaseHas('gruppi_persone', ['persona_id' => $persona->id, 'data_entrata_gruppo' => '2000-01-01', 'data_uscita_gruppo' => '2005-01-01'], 'db_nomadelfia');
    $this->assertDatabaseMissing('persone_posizioni', ['persona_id' => $persona->id, 'data_inizio' => '2010-03-01'], 'db_nomadelfia');
});

it('rejects a data di entrata after the end of a posizione that started with the entrata', function (): void {
    login();
    $persona = Persona::factory()->nato(Carbon::parse('1970-05-01'))->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2010-03-01'), GruppoFamiliare::all()->random());
    $persona->assegnaPosizione(Posizione::perNome('postulante'), Carbon::parse('2011-01-01'));

    $this->put(route('nomadelfia.join.update', ['id' => $persona->id, 'entrata' => '2010-03-01']), ['data_entrata' => '2011-06-01'])
        ->assertSessionHasErrors(['data_entrata' => 'La data di entrata non può essere successiva alla fine di Posizione: Ospite (2011-01-01).']);

    $this->assertDatabaseHas('popolazione', ['persona_id' => $persona->id, 'data_entrata' => '2010-03-01'], 'db_nomadelfia');
});
