<?php

use App\Admin\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function Pest\Laravel\actingAs;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Tests\TestCasePest;

uses(TestCasePest::class)->in('Biblioteca', 'Scuola', 'Popolazione', 'Officina', 'AdminSys');

function login(User $user = null): User
{
    if (is_null($user)) {
        $user = User::where('username', '=', 'Admin')->first();
    }

    actingAs($user);

    return $user;
}

function runMiddleware($middleware, $permission)
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
