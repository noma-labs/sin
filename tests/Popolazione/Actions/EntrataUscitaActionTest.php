<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;

it('entrata_minorenne_con_famiglia', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->assegnaCapoFamiglia($capoFam, $data_entrata);

    $action = app(EntrataMinorenneConFamigliaAction::class);
    $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));

    expect($persona->isPersonaInterna())->toBeTrue();
    expect($persona->getDataEntrataNomadelfia())->tobe($data_entrata);
    //        $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
    expect($persona->posizioneAttuale()->pivot->data_inizio)->tobe($data_entrata);
    //        expect($persona->statoAttuale()->id, $nubile->id);
    //        expect($persona->statoAttuale()->stato, $nubile->stato);
    expect($persona->statoAttuale()->pivot->data_inizio)->tobe($persona->data_nascita);
    expect($persona->gruppofamiliareAttuale()->id)->tobe($gruppo->id);
    expect($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->tobe($data_entrata);
    expect($persona->famigliaAttuale())->not->toBeNull();
    expect($persona->famigliaAttuale()->pivot->data_entrata)->tobe($persona->data_nascita);

});

it('entrata_minorenne_accolto', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->assegnaCapoFamiglia($capoFam, $data_entrata);

    $action = app(EntrataMinorenneAccoltoAction::class);
    $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));

    expect($persona->isPersonaInterna())->toBeTrue();
});
