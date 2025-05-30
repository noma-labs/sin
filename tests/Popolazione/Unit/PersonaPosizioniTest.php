<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Carbon\Carbon;

it('assign posizione', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $data_inizio = Carbon::now()->addYears(5)->startOfDay();
    $data_fine = Carbon::now()->addYears(3)->startOfDay();
    $postulante = Posizione::perNome('postulante');

    $persona->assegnaPosizione($postulante, $data_inizio, $data_fine);

    expect($persona->posizioneAttuale()->id)->toBe($postulante->id)
        ->and($persona->posizioniStorico()->first()->pivot->data_fine)->toEqual($data_fine->toDateString());
});

it('edit date of posizione', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $data_inizio = Carbon::now()->addYears(5)->startOfDay();
    $data_fine = Carbon::now()->addYears(3)->startOfDay();
    $postulante = Posizione::perNome('postulante');

    $persona->assegnaPosizione($postulante, $data_inizio, $data_fine);
    $new_data_inizio = Carbon::now()->addYears(6)->startOfDay();

    $persona->modificaDataInizioPosizione($postulante->id, $data_inizio, $new_data_inizio);

    expect($persona->posizioneAttuale()->id)->toBe($postulante->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toEqual($new_data_inizio->toDateString());
    // expect($persona->posizioneAttuale()->pivot->data_fine, $data_fine);
});
