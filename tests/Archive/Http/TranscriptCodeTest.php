<?php

declare(strict_types=1);

use App\Http\Archive\TranscriptCode;

it('parses transcript code parts from dataset', function (
    string $rawCode,
    int $year,
    int $month,
    int $day,
    string $hour,
    string $normalizedCode,
): void {
    $code = TranscriptCode::fromString($rawCode);

    expect($code->year)->toBe($year)
        ->and($code->month)->toBe($month)
        ->and($code->day)->toBe($day)
        ->and($code->hour)->toBe($hour)
        ->and($code->toString())->toBe($normalizedCode);
})->with([
    'without hour part' => ['501216', 50, 12, 16, '0A', '5012160A'],
    'with numeric hour' => ['50121600', 50, 12, 16, '00', '50121600'],
    'with 0A suffix hour' => ['4912070A', 49, 12, 7, '0A', '4912070A'],
    'with 00A suffix hour' => ['49110800A', 49, 11, 8, '0A', '4911080A'],
    'with 00B suffix hour' => ['49110800B', 49, 11, 8, '0B', '4911080B'],
]);


it('throws for codes shorter than six characters', function (string $rawCode): void {
    expect(fn () => TranscriptCode::fromString($rawCode))
        ->toThrow(InvalidArgumentException::class);
})->with([
    'empty string' => [''],
    'five digits' => ['12345'],
    'bad hour digits' => ['4912230-a'],
]);

it('accepts int year month and day in constructor', function (): void {
    $code = new TranscriptCode(year: 5, month: 1, day: 9, hour: null);

    expect($code->year)->toBe(5)
        ->and($code->month)->toBe(1)
        ->and($code->day)->toBe(9)
        ->and($code->hour)->toBe('0A')
        ->and($code->toString())->toBe('0501090A');
});

it('validates year month and day constructor constraints', function (
    int $year,
    int $month,
    int $day,
): void {
    expect(fn () => new TranscriptCode(year: $year, month: $month, day: $day, hour: '0A'))
        ->toThrow(InvalidArgumentException::class);
})->with([
    'year too low' => [0, 1, 1],
    'year too high' => [100, 1, 1],
    'month too low' => [1, 0, 1],
    'month too high' => [1, 13, 1],
    'day too low' => [1, 1, 0],
    'day too high' => [1, 1, 32],
]);
