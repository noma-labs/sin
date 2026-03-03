<?php

declare(strict_types=1);

namespace Tests\Http\Patente;

use App\Patente\Models\Patente;

use App\Patente\Controllers\PatenteController;
use App\Patente\Controllers\PatenteElenchiController;

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
    login();
    $patente = Patente::factory()->create();

    $response = $this->delete(route('patente.elimina', ['numero' => $patente->numero_patente]));
    $response->assertRedirect(route('patente.scadenze'));

    $patente->refresh();
    expect($patente->deleted_at)->not->toBeNull();
    expect(Carbon::parse($patente->deleted_at)->isToday())->toBeTrue();
});
