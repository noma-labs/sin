<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\ChangeGruppoFamiliareAction;
use Carbon\Carbon;

it('change a gruppo of a person', function (): void {
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $gruppo = GruppoFamiliare::first();
    $persona->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata->toDatestring()]);

    $newGruppo = GruppoFamiliare::factory()->create();
    $newGruppoEntrata = Carbon::now()->addMonth();
    $uscita = Carbon::now()->addDays(15);

    $action = app(ChangeGruppoFamiliareAction::class);
    $action->execute($persona, $gruppo, $data_entrata, $uscita, $newGruppo, $newGruppoEntrata);

    expect($persona->gruppofamiliariStorico()->first()->id)->tobe($gruppo->id);
    expect($persona->gruppofamiliariStorico()->first()->pivot->data_entrata_gruppo)->tobe($data_entrata->toDatestring());
    expect($persona->gruppofamiliariStorico()->first()->pivot->data_uscita_gruppo)->tobe($uscita->toDatestring());
    expect($persona->gruppofamiliareAttuale()->id)->tobe($newGruppo->id);
    expect($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->tobe($newGruppoEntrata->toDatestring());

});
