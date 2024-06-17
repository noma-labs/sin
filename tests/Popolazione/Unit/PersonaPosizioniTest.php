<?php

namespace Tests\Unit;

use Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;

it('testAssignPosizione', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $data_inizio = Carbon::now()->addYears(5)->toDatestring();
    $data_fine = Carbon::now()->addYears(3)->toDatestring();
    $postulante = Posizione::perNome('postulante');

    $persona->assegnaPosizione($postulante, $data_inizio, $data_fine);

    expect($persona->posizioneAttuale()->id)->toBe($postulante->id)
        ->and($persona->posizioniStorico()->first()->pivot->data_fine)->toBe($data_fine);
});

it('testModificaDataPosizione', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $data_inizio = Carbon::now()->addYears(5)->toDatestring();
    $data_fine = Carbon::now()->addYears(3)->toDatestring();
    $postulante = Posizione::perNome('postulante');

    $persona->assegnaPosizione($postulante, $data_inizio, $data_fine);
    $new_data_inizio = Carbon::now()->addYears(6)->toDatestring();

    $persona->modificaDataInizioPosizione($postulante->id, $data_inizio, $new_data_inizio);

    expect($persona->posizioneAttuale()->id)->toBe($postulante->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toBe($new_data_inizio);
    // expect($persona->posizioneAttuale()->pivot->data_fine, $data_fine);
});
