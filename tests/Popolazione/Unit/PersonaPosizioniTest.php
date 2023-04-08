<?php

namespace Tests\Unit;

use Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SaveEntrataInNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;

it('testAssignPosizione', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = new EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $data_inizio = Carbon::now()->addYears(5)->toDatestring();
    $data_fine = Carbon::now()->addYears(3)->toDatestring();
    $postulante = Posizione::perNome('postulante');

    $persona->assegnaPosizione($postulante, $data_inizio, $data_fine);

    $this->assertEquals($persona->posizioneAttuale()->id, $postulante->id);
    $this->assertEquals($persona->posizioniStorico()->first()->pivot->data_fine, $data_fine);
});

it('testModificaDataPosizione', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = new EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $data_inizio = Carbon::now()->addYears(5)->toDatestring();
    $data_fine = Carbon::now()->addYears(3)->toDatestring();
    $postulante = Posizione::perNome('postulante');

    $persona->assegnaPosizione($postulante, $data_inizio, $data_fine);
    $new_data_inizio = Carbon::now()->addYears(6)->toDatestring();

    $persona->modificaDataInizioPosizione($postulante->id, $data_inizio, $new_data_inizio);

    $this->assertEquals($persona->posizioneAttuale()->id, $postulante->id);
    $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $new_data_inizio);
    // $this->assertEquals($persona->posizioneAttuale()->pivot->data_fine, $data_fine);
});
