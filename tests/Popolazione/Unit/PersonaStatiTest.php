<?php

namespace Tests\Unit;

use Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataInNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

it('testAssignStatoSacerdote', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $data_inizio = Carbon::now()->addYears(5)->toDatestring();
    $data_fine = Carbon::now()->addYears(3)->toDatestring();
    $sac = Stato::perNome('sacerdote');

    $persona->assegnaStato($sac, $data_inizio, $data_fine);

    expect($persona->statoAttuale()->id)->toBe($sac->id)
        ->and($persona->statiStorico()->first()->pivot->data_fine)->toBe($data_fine);
});
