<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\PopolazioneNomadelfia\Controllers\AggiornamentoAnagrafeController;
use Carbon\Carbon;
use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\LogEntrataPersonaAction;

it('show aggiornamento anagrafe index', function (): void {
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA53')->luogoNascita('grosseto')->create();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::first();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata->toDateString()]);
    $famiglia->assegnaCapoFamiglia($capoFam);

    $action = app(LogEntrataPersonaAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $gruppo,
        $famiglia
    );
    login();
    $this->get(action([AggiornamentoAnagrafeController::class, 'index']))
        ->assertSuccessful()
        ->assertSee($persona->nominativo);
});
