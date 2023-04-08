<?php

namespace App\AdminSys\Http;

use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Spatie\Permission\Models\Role;

it("no_logged_in_user_return_unhautorize", function(){
        $middleare = app(PermissionMiddleware::class);

        $this->assertEquals(
            403,
            runMiddleware($middleare, "popolazione.persona.visualizza")
        );
    });

it("logged_in_user_return_unhautorize", function(){
        $middleare = app(PermissionMiddleware::class);

        login();

        $this->assertEquals(
            200,
            runMiddleware($middleare, "popolazione.persona.visualizza")
        );

    });

it("loged_in_user_can_view_index", function(){
        $this->get(action([PopolazioneNomadelfiaController::class, 'index']))->assertForbidden();

        $utente = Role::findByName("super-admin")->users()->first();

        login($utente);

        $this->get(action([PopolazioneNomadelfiaController::class, 'index']))->assertSuccessful();
    });


