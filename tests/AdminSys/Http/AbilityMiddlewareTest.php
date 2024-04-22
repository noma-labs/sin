<?php

namespace App\AdminSys\Http;

use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneSummaryController;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Spatie\Permission\Models\Role;

it('forbids guests user', function () {
    $middleware = app(PermissionMiddleware::class);

    $this->assertEquals(
        403,
        runMiddleware($middleware, 'popolazione.persona.visualizza')
    );
});

it('allows logged in user', function () {
    $middleware = app(PermissionMiddleware::class);

    login();

    $this->assertEquals(
        200,
        runMiddleware($middleware, 'popolazione.persona.visualizza')
    );

});

it('allows super-admin user to see', function () {
    $this->get(action([PopolazioneSummaryController::class, 'index']))->assertRedirect(route('login'));

    $utente = Role::findByName('super-admin')->users()->first();

    login($utente);

    $this->get(action([PopolazioneSummaryController::class, 'index']))->assertSuccessful();
});
