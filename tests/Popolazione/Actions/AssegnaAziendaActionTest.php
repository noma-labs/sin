<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;
use Carbon\Carbon;

it('assign a lavoratore azienda to a person', function (): void {
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $azienda = Azienda::factory()->create();

    $action = app(AssegnaAziendaAction::class);
    $action->execute($persona, $azienda, $data_entrata, Azienda::MANSIONE_LAVORATORE);

    expect($persona->aziendeAttuali()->first()->id)->tobe($azienda->id);
    expect($persona->aziendeAttuali()->first()->pivot->mansione)->tobe(Azienda::MANSIONE_LAVORATORE);
});

it('assign a responsabile azienda to a person', function (): void {
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $azienda = Azienda::factory()->create();

    $action = app(AssegnaAziendaAction::class);
    $action->execute($persona, $azienda, $data_entrata, Azienda::MANSIONE_RESPONSABILE);

    expect($persona->aziendeAttuali()->first()->id)->tobe($azienda->id);
    expect($persona->aziendeAttuali()->first()->pivot->mansione)->tobe(Azienda::MANSIONE_RESPONSABILE);
});
