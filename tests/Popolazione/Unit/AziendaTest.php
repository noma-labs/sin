<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;

it('assign a worker', function (): void {
    $azienda = Azienda::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    expect($azienda->lavoratori()->count())->toBe(0);
    expect($azienda->lavoratoriAttuali()->count())->toBe(0);
    expect($azienda->lavoratoriStorici()->count())->toBe(0);
    expect($persona->aziendeAttuali()->count())->toBe(0);
    expect($persona->aziendeStorico()->count())->toBe(0);

    $data_inizio = Carbon::now()->addYears(5);
    $action = new AssegnaAziendaAction;
    $action->execute($persona, $azienda, $data_inizio, Azienda::MANSIONE_LAVORATORE);

    expect($azienda->lavoratoriAttuali()->count())->toBe(1);

    $resp = Persona::factory()->maggiorenne()->maschio()->create();
    $action->execute($resp, $azienda, $data_inizio, Azienda::MANSIONE_RESPONSABILE);
    expect($azienda->lavoratoriAttuali()->count())->toBe(2);

    $data_uscita = Carbon::now()->addYears(5)->toDatestring();

    $act = app(UscitaPersonaAction::class);
    $act->execute($persona, $data_uscita);

    expect($azienda->lavoratoriAttuali()->count())->toBe(1);
});
