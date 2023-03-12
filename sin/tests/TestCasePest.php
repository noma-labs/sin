<?php

namespace Tests;

use App\Admin\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Http\Request;

abstract class TestCasePest extends BaseTestCase
{
    use CreatesApplication;

    public function createRequest($method, $uri): Request
    {
        $symfonyRequest = SymfonyRequest::create(
            $uri,
            $method
        );

        return Request::createFromBase($symfonyRequest);
    }
}
