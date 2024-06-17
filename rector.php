<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPreparedSets(deadCode: true, typeDeclarations: true)
    ->withSets([
        LaravelSetList::LARAVEL_90,
        LaravelSetList::LARAVEL_100,
    ]);
