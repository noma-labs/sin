<?php

declare(strict_types=1);

namespace Tests\Scuola\Unit;

use App\Scuola\DataTransferObjects\Dimensione;

it("parse the dimension from a string", function (string $dimension, int $width, int $high, string $outString): void {
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
])->only();
