<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\GruppoFamiliare\Controllers\CapogruppoController;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Carbon\Carbon;

it('can assign a capogruppo to a gruppo familiare', function (): void {
    login();
    $gruppo = GruppoFamiliare::factory()->create();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, $gruppo);
    $persona->assegnaPosizione(Posizione::perNome('postulante'), $data_entrata);
    $persona->assegnaPosizione(Posizione::perNome('effettivo'), $data_entrata);

    $this->post(action([CapogruppoController::class, 'store'], $gruppo->id), [
        'nuovo' => $persona->id,
        'inizio' => Carbon::now()->toDateString(),
    ])->assertRedirect();

    expect($gruppo->capogruppoAttuale()->id)->toBe($persona->id);
});

it('rejects a non-existent persona id', function (): void {
    login();
    $gruppo = GruppoFamiliare::factory()->create();

    $this->post(action([CapogruppoController::class, 'store'], $gruppo->id), [
        'nuovo' => 999999999,
        'inizio' => Carbon::now()->toDateString(),
    ])->assertSessionHasErrors('nuovo');
});

it('requires nuovo and inizio', function (): void {
    login();
    $gruppo = GruppoFamiliare::factory()->create();

    $this->post(action([CapogruppoController::class, 'store'], $gruppo->id), [])
        ->assertSessionHasErrors(['nuovo' => 'Il nuovo capogruppo è abbligatoripo', 'inizio' => 'La data di inizio è obbligatoria']);
});
