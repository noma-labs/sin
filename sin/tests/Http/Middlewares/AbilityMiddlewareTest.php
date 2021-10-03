<?php

namespace App\Http\Middleware;

use App\Admin\Models\Ruolo;
use App\Nomadelfia\Controllers\PopolazioneNomadelfiaController;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;

class AbilityMiddlewareTest extends TestCase
{
    /** @test */
    public function test_middleware_in_isolation()
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
    public function test_middleware_as_integration()
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
