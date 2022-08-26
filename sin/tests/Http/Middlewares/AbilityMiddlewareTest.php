<?php

namespace App\Http\Middleware;

use App\Admin\Models\Ruolo;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;

class AbilityMiddlewareTest extends TestCase
{
    /** @test */
    public function no_loged_in_user_return_unhautorize()
    {
        $middleare = app(AbilityMiddleware::class);

        $this->assertEquals(
            403,
            $this->runMiddleware($middleare, "persona.visualizza")
        );

        $this->login();

        $this->assertEquals(
            200,
            $this->runMiddleware($middleare, "persona.visualizza")
        );

    }

    /** @test */
    public function loged_in_user_can_view_index()
    {
        $this->get(action([PopolazioneNomadelfiaController::class, 'index']))->assertForbidden();

        $utente = Ruolo::findByName("Admin")->utenti->first();

        $this->login($utente);

        $this->get(action([PopolazioneNomadelfiaController::class, 'index']))->assertSuccessful();
    }

    protected function runMiddleware($middleware, $permission)
    {
        try {
            return $middleware->handle(
                new Request(),
                function () {
                    return (new Response())->setContent('<html></html>');
                },
                $permission
            )->status();
        } catch (UnauthorizedException $e) {
            return $e->getStatusCode();
        }
    }
}
