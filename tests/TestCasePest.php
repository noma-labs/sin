<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

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

    protected function emptyTempDirectory(string $tempDirPath)
    {
        if (!is_dir($tempDirPath)) {
            mkdir($tempDirPath);
        }
        $files = scandir($tempDirPath);

        foreach ($files as $file) {
            if (!in_array($file, ['.', '..', '.gitignore'])) {
                unlink("{$tempDirPath}/{$file}");
            }
        }
    }
}
