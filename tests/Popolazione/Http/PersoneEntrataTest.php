<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersoneController;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataInNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

it('it_can_insert_minorenne_accolto_nella_popolazione', function () {
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $data_entrata = Carbon::now()->toDatestring();
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, $data_entrata, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam, $data_entrata);

    login();
    $this->withoutExceptionHandling();
    $this->post(action([PersoneController::class, 'insertPersonaInterna'], ['idPersona' => $persona->id]),
        [
            'tipologia' => 'minorenne_accolto',
            'data_entrata' => $data_entrata,
            'famiglia_id' => $famiglia->id,
        ]);
//            ->assertSee("inserita correttamente.");

    $persona = Persona::findOrFail($persona->id);
    $figlio = Posizione::perNome('figlio');
    $nubile = Stato::perNome('celibe');
    $this->assertTrue($persona->isPersonaInterna());
    $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
    $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
    $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
    $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
    $this->assertEquals($persona->statoAttuale()->stato, $nubile->stato);
    $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
    $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
    $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);
    $this->assertNotNull($persona->famigliaAttuale());
    $this->assertEquals($persona->famigliaAttuale()->pivot->data_entrata, $data_entrata);
    $this->assertEquals($persona->famigliaAttuale()->pivot->posizione_famiglia, Famiglia::getFiglioAccoltoEnum());
});

it('it_can_insert_minorenne_con_famiglia_nella_popolazione', function () {
    $data_nascita = Carbon::now();
    $persona = Persona::factory()->minorenne()->nato($data_nascita)->maschio()->create();
    $data_entrata = Carbon::now()->toDatestring();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, $data_entrata, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam, $data_entrata);

    login();
    $this->post(action([PersoneController::class, 'insertPersonaInterna'], ['idPersona' => $persona->id]),
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
    $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
    $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
    $this->assertEquals($persona->statoAttuale()->stato, $nubile->stato);
    $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
    $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
    $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);
    $this->assertNotNull($persona->famigliaAttuale());
    $this->assertEquals($persona->famigliaAttuale()->pivot->data_entrata, $data_nascita->toDateString());
    $this->assertEquals($persona->famigliaAttuale()->pivot->posizione_famiglia, Famiglia::getFiglioNatoEnum());
});

it('entrata_persona_dalla_nascita', function () {
    $data_nascita = Carbon::now();
    $persona = Persona::factory()->minorenne()->nato($data_nascita)->femmina()->create();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, Carbon::now()->toDatestring(), $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam, Carbon::now()->toDatestring());

    login();
    $this->withoutExceptionHandling();
    $this->post(action([PersoneController::class, 'insertPersonaInterna'], ['idPersona' => $persona->id]),
        [
            'tipologia' => 'dalla_nascita',
            'famiglia_id' => $famiglia->id,
        ]);

    $this->assertTrue($persona->isPersonaInterna());
    $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_nascita->toDatestring());
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
    $this->assertEquals($persona->famigliaAttuale()->pivot->data_entrata, $data_nascita->toDatestring());
    $this->assertEquals($persona->famigliaAttuale()->pivot->posizione_famiglia, Famiglia::getFiglioNatoEnum());
});

it('entrata_maggiorenne_single', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $ospite = Posizione::perNome('ospite');
    $celibe = Stato::perNome('celibe');

    login();
    $this->post(action([PersoneController::class, 'insertPersonaInterna'], ['idPersona' => $persona->id]),
        [
            'tipologia' => 'maggiorenne_single',
            'gruppo_id' => $gruppo->id,
            'data_entrata' => $data_entrata,
        ]);

    $this->assertTrue($persona->isPersonaInterna());
    $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
    $this->assertEquals($persona->posizioneAttuale()->id, $ospite->id);
    $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
    $this->assertEquals($persona->statoAttuale()->id, $celibe->id);
    $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
    $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
    $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);

    $this->assertNotNull($persona->famigliaAttuale());
    // check that the date creation of the family is when the person is 18 years old.
    $this->assertEquals($persona->famigliaAttuale()->data_creazione,
        Carbon::parse($persona->data_nascita)->addYears(18)->toDatestring());
    $this->assertEquals($persona->famigliaAttuale()->pivot->posizione_famiglia, Famiglia::getSingleEnum());
}
);

it('entrata_maggiorenne_sposato', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->create();
    $gruppo = GruppoFamiliare::first();
    $ospite = Posizione::perNome('ospite');

    login();
    $this->post(action([PersoneController::class, 'insertPersonaInterna'], ['idPersona' => $persona->id]),
        [
            'tipologia' => 'maggiorenne_famiglia',
            'gruppo_id' => $gruppo->id,
            'data_entrata' => $data_entrata,
        ]);

    $this->assertTrue($persona->isPersonaInterna());
    $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
    $this->assertEquals($persona->posizioneAttuale()->id, $ospite->id);
    $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
    $this->assertEquals($persona->statoAttuale(), null);
    $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
    $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);

    $this->assertNull($persona->famigliaAttuale());
});
