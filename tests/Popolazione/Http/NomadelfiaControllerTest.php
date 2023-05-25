<?php

namespace Tests\Http\Controllers;

use App\Nomadelfia\Azienda\Controllers\AziendeController;
use App\Nomadelfia\GruppoFamiliare\Controllers\GruppifamiliariController;
use App\Nomadelfia\Incarico\Controllers\IncarichiController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneSummaryController;
use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;

it('only_admin_can_see_nomadelfia_system', function () {
    $this->withExceptionHandling();

    $this
        ->get(action([PopolazioneSummaryController::class, 'index']))
        ->assertForbidden();

    login();

    $this
        ->get(action([PopolazioneSummaryController::class, 'index']))
        ->assertSuccessful();
});

it('show_popolazione_summary', function () {
    $this->withExceptionHandling();

    login();

    $this
        ->get(action([PopolazioneSummaryController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('Gestione Popolazione')
        ->assertSee('Gestione Famiglie')
        ->assertSee('Gestione Gruppi Familiari');
});

it('show_incarichi_index', function () {
    $this->withExceptionHandling();

    login();

    $incarico = Incarico::factory()->create();

    $this
        ->get(action([IncarichiController::class, 'view']))
        ->assertSuccessful()
        ->assertSee($incarico->nome);

});

it('show_aziende_index', function () {
    $this->withExceptionHandling();

    login();

    $a = Azienda::factory()->create();

    $this
        ->get(action([AziendeController::class, 'view']))
        ->assertSuccessful()
        ->assertSee($a->nome_azienda);

    $this
        ->get(action([AziendeController::class, 'edit'], $a->id))
        ->assertSuccessful()
        ->assertSee($a->nome_azienda);

});

it('show_gruppifamiliari_edit', function () {
    $this->withExceptionHandling();

    login();

    $gruppo = GruppoFamiliare::factory()->create();
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();

    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $this
        ->get(action([GruppifamiliariController::class, 'view']))
        ->assertSuccessful()
        ->assertSee($gruppo->nome);

    $this
        ->get(action([GruppifamiliariController::class, 'edit'], $gruppo->id))
        ->assertSuccessful()
        ->assertSee($gruppo->nome);
});
