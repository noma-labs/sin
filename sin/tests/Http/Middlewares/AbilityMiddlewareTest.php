<?php

namespace App\Http\Middleware;

use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use Illuminate\Http\Response;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class AbilityMiddlewareTest extends TestCase
{
    /** @test */
    public function no_logged_in_user_return_unhautorize()
    {
        $middleare = app(PermissionMiddleware::class);

        $this->assertEquals(
            403,
            $this->runMiddleware($middleare, "popolazione.persona.visualizza")
        );
    }

    /** @test */
    public function logged_in_user_return_unhautorize()
    {
        $middleare = app(PermissionMiddleware::class);

        $this->login();

        $this->assertEquals(
            200,
            $this->runMiddleware($middleare, "popolazione.persona.visualizza")
        );

    }

    /** @test */
    public function loged_in_user_can_view_index()
    {
        $this->get(action([PopolazioneNomadelfiaController::class, 'index']))->assertForbidden();

        $utente = Role::findByName("super-admin")->users()->first();

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
