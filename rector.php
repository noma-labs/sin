<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPreparedSets(deadCode: true, typeDeclarations: true)
    ->withSets([
        SetList::INSTANCEOF,
        LaravelSetList::LARAVEL_90,
        LaravelSetList::LARAVEL_100,
        SetList::PHP_80,
    ]);
