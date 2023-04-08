<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Http\Request;

abstract class TestCasePest extends BaseTestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function createRequest($method, $uri): Request
    {
        $symfonyRequest = SymfonyRequest::create(
            $uri,
            $method
        );

        return Request::createFromBase($symfonyRequest);
    }
}
