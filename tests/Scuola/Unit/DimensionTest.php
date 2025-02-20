<?php

declare(strict_types=1);

namespace Tests\Scuola\Unit;

use App\Scuola\DataTransferObjects\Dimensione;
use App\Scuola\Exceptions\BadDimensionException;

it('parse the dimension from a string', function (string $dimension, int $width, int $high, string $outString): void {
    $parsed = Dimensione::fromString($dimension);

    expect($parsed->getHeight())->toEqual($high);
    expect($parsed->getWidth())->toEqual($width);
    expect($parsed->toString())->toEqual($outString);
})->with([
    ['210x290', 210, 290, '210x290'],
    ['210x297', 210, 297, '210x297'],
    ['a4', 210, 297, '210x297'],
    ['A4', 210, 297, '210x297'],
    ['A3', 297, 420, '297x420'],
    ['a3', 297, 420, '297x420'],
    [' 210 x 297 ', 210, 297, '210x297'],
    ['210x297mm', 210, 297, '210x297'],
]);

it('throws an exception if the dimension is wrong', function (string $dimension): void {
     Dimensione::fromString($dimension);
})
->with([
    '23 45',
    '23x',
    '23x45x67',
    '23xtwenty',
    'twentyx45',
])
->throws(BadDimensionException::class);
