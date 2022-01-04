<?php

namespace Tests;

use App\Admin\Models\Risorsa;
use App\Admin\Models\Ruolo;
use App\Admin\Models\Sistema;
use App\Admin\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Http\Request;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function login(User $user = null): User
    {
        if (is_null($user)) {
            $user = User::where( 'username', "=", 'Admin')->first();
        }

        $this->actingAs($user);

        return $user;
    }

    public function createRequest($method, $uri): Request
    {
        $symfonyRequest = SymfonyRequest::create(
            $uri,
            $method
        );

        return Request::createFromBase($symfonyRequest);
    }
}
