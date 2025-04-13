<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Controllers\PersonaEntrataController;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;

it('can show the form to enter a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaEntrataController::class, 'create'], ['idPersona' => $persona->id]))
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
    $this->post(action([PersonaEntrataController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'tipologia' => 'minorenne_accolto',
            'data_entrata' => $data_entrata->toDatestring(),
            'famiglia_id' => $famiglia->id,
        ]);
    //            ->assertSee("inserita correttamente.");Controllers

    $persona = Persona::findOrFail($persona->id);
    $figlio = Posizione::perNome('figlio');
    $nubile = Stato::perNome('celibe');
    $this->assertTrue($persona->isPersonaInterna());
    $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
    $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
    $this->assertEquals(Carbon::parse($persona->posizioneAttuale()->pivot->data_inizio), $data_entrata);
    $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
    $this->assertEquals($persona->statoAttuale()->stato, $nubile->stato);
    $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
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
    $this->post(action([PersonaEntrataController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'tipologia' => 'minorenne_famiglia',
            'data_entrata' => $data_entrata,
            'famiglia_id' => $famiglia->id,
        ]);

    $this->assertTrue($persona->isPersonaInterna());
    $figlio = Posizione::perNome('figlio');
    $nubile = Stato::perNome('celibe');
    $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
    $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
    $this->assertEquals(Carbon::parse($persona->posizioneAttuale()->pivot->data_inizio), $data_entrata);
    $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
    $this->assertEquals($persona->statoAttuale()->stato, $nubile->stato);
    $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
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
    $this->post(action([PersonaEntrataController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'tipologia' => 'dalla_nascita',
            'famiglia_id' => $famiglia->id,
        ]);

    $this->assertTrue($persona->isPersonaInterna());
    $this->assertEquals(Carbon::parse($persona->getDataEntrataNomadelfia()), $data_nascita);
    $figlio = Posizione::perNome('figlio');
    $nubile = Stato::perNome('nubile');
    $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
    $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_nascita->toDatestring());
    $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
    $this->assertEquals($persona->statoAttuale()->stato, $nubile->stato);
    $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
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
    $celibe = Stato::perNome('celibe');

    login();

    $this->post(action([PersonaEntrataController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'tipologia' => 'maggiorenne_single',
            'gruppo_id' => $gruppo->id,
            'data_entrata' => $data_entrata->toDateString(),
        ]);

    expect($persona->isPersonaInterna())->toBeTrue()
        ->and($persona->getDataEntrataNomadelfia())->toEqual($data_entrata)
        ->and($persona->posizioneAttuale()->id)->toEqual($ospite->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toEqual($data_entrata->toDateString())
        ->and($persona->statoAttuale()->id)->toEqual($celibe->id)
        ->and($persona->statoAttuale()->pivot->data_inizio)->toEqual($persona->data_nascita)
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
    $this->post(action([PersonaEntrataController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'tipologia' => 'maggiorenne_famiglia',
            'gruppo_id' => $gruppo->id,
            'data_entrata' => $data_entrata->toDatestring(),
        ]);

    $this->assertTrue($persona->isPersonaInterna());
    $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
    $this->assertEquals($persona->posizioneAttuale()->id, $ospite->id);
    $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata->toDateString());
    $this->assertEquals($persona->statoAttuale(), null);
    $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
    $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata->toDateString());
    $this->assertNull($persona->famigliaAttuale());
});
