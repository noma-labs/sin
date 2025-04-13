<?php

declare(strict_types=1);

namespace Tests\Http\Controllers;

use App\Nomadelfia\Azienda\Controllers\AziendeController;
use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\GruppoFamiliare\Controllers\GruppifamiliariController;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Incarico\Controllers\IncarichiController;
use App\Nomadelfia\Incarico\Models\Incarico;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneSummaryController;
use Carbon\Carbon;

it('only_admin_can_see_nomadelfia_system', function (): void {
    $this->withExceptionHandling();

    $this
        ->get(action([PopolazioneSummaryController::class, 'index']))
        ->assertRedirect(route('login'));

    login();

    $this
        ->get(action([PopolazioneSummaryController::class, 'index']))
        ->assertSuccessful();
});

it('show_popolazione_summary', function (): void {
    $this->withExceptionHandling();

    login();

    $this
        ->get(action([PopolazioneSummaryController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('Gestione Popolazione')
        ->assertSee('Gestione Famiglie')
        ->assertSee('Gestione Gruppi Familiari');
});

it('show_incarichi_index', function (): void {
    $this->withExceptionHandling();

    login();

    $incarico = Incarico::factory()->create();

    $this
        ->get(action([IncarichiController::class, 'view']))
        ->assertSuccessful()
        ->assertSee($incarico->nome);

});

it('show_aziende_index', function (): void {
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

it('show_gruppifamiliari_edit', function (): void {
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
