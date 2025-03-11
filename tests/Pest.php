<?php

declare(strict_types=1);

use App\Admin\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Models\Role;
use Tests\TestCasePest;

use function Pest\Laravel\actingAs;

pest()->extends(TestCasePest::class)
    ->in('Agraria', 'Biblioteca', 'Scuola', 'Popolazione', 'Officina', 'AdminSys', 'Photo', 'Livewire', 'Patente');

function login(?User $user = null): User
{
    if (is_null($user)) {
        $user = User::firstOrNew(['username' => 'admin-test'],[ 'email' => 'admin-test@email.it', 'password' => 'admin-test', 'persona_id' => 0]);
        $user->save();
        $user->assignRole(Role::where('name', "=", 'super-admin')->firstOrFail());
    }

    actingAs($user);

    return $user;
}

function runMiddleware($middleware, $permission)
{
    try {
        return $middleware->handle(
            new Request,
            function (): Response {
                return (new Response)->setContent('<html></html>');
            },
            $permission
        )->status();
    } catch (UnauthorizedException $e) {
        return $e->getStatusCode();
    }
}
