<?php

declare(strict_types=1);

namespace Tests\Http\Patente;

use App\Patente\Controllers\PatenteController;
use App\Patente\Controllers\PatenteElenchiController;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Patente\Models\Patente;
use App\Officina\Models\ViewClienti;
use Carbon\Carbon;


it('forbids access to guests', function (): void {
    $this->get(action([PatenteController::class, 'index']))
        ->assertRedirect(route('login'));
});

it('shows patenti to logged users', function (): void {
    login();
    $this->get(action([PatenteController::class, 'index']))
        ->assertOk();
});

it('export into pdf', function (): void {
    login();

    $this->get(action([PatenteElenchiController::class, 'stampaAutorizzati']))
        ->assertSuccessful()
        ->assertDownload();
});

it('soft deletes patente and sets deleted_at', function () {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::now(), GruppoFamiliare::all()->random());
    $patente = Patente::factory()->persona($persona)->create();
    $patente->refresh();

    login();

    $response = $this->delete(action([PatenteController::class, 'delete'], ['numero' => $patente->numero_patente]));
    $response->assertRedirect(route('patente.scadenze'));

    $patente = Patente::withoutGlobalScope('InNomadelfia')->withTrashed()->where('numero_patente', $patente->numero_patente)->first();
    expect($patente->deleted_at)->not->toBeNull();
    expect(Carbon::parse($patente->deleted_at)->isToday())->toBeTrue();

    $r = ViewClienti::query()->select('cliente_con_patente')->where('id', $persona->id)->first();
    expect($r->cliente_con_patente)->toBeNull();
});
