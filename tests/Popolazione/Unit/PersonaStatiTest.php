<?php

declare(strict_types=1);

namespace Tests\Unit;

use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

it('assign sacerdote as stato', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $data_inizio = Carbon::now()->addYears(5)->toDatestring();
    $data_fine = Carbon::now()->addYears(3)->toDatestring();
    $persona->assegnaStato(Stato::perNome('sacerdote'), $data_inizio, $data_fine);

    expect($persona->statoAttuale()->id)->toBe(Stato::perNome('sacerdote')->id)
        ->and($persona->statiStorico()->first()->pivot->data_fine)->toBe($data_fine);
});
