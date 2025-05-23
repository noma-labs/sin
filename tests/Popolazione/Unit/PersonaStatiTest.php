<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;

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
