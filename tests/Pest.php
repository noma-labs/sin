<?php

use App\Admin\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Tests\TestCasePest;

use function Pest\Laravel\actingAs;

uses(TestCasePest::class)->in('Biblioteca', 'Scuola', 'Popolazione', 'Officina', 'AdminSys', 'Photo', 'Livewire', 'Patente');

function login(?User $user = null): User
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
            function (): \Illuminate\Http\Response {
                return (new Response())->setContent('<html></html>');
            },
            $permission
        )->status();
    } catch (UnauthorizedException $e) {
        return $e->getStatusCode();
    }
}
