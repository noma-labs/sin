<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\ChangeGruppoFamiliareAction;

it('change a gruppo of a person', function (): void {
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $gruppo = GruppoFamiliare::first();
    $persona->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata->toDatestring()]);

    $newGruppo = GruppoFamiliare::factory()->create();
    $newGruppoEntrata = Carbon::now()->addMonth(1);
    $uscita = Carbon::now()->addDays(15);

    $action = app(ChangeGruppoFamiliareAction::class);
    $action->execute($persona, $gruppo, $data_entrata, $uscita, $newGruppo, $newGruppoEntrata);

    expect($persona->gruppofamiliariStorico()->first()->id)->tobe($gruppo->id);
    expect($persona->gruppofamiliariStorico()->first()->pivot->data_entrata_gruppo)->tobe($data_entrata->toDatestring());
    expect($persona->gruppofamiliariStorico()->first()->pivot->data_uscita_gruppo)->tobe($uscita->toDatestring());
    expect($persona->gruppofamiliareAttuale()->id)->tobe($newGruppo->id);
    expect($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->tobe($newGruppoEntrata->toDatestring());

});
