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
    'without hour part' => ['501216', 1950, 12, 16, '0A', '5012160A'],
    'with unknown hour placeholder' => ['501216??', 1950, 12, 16, '0A', '5012160A'],
    'with single letter hour' => ['501216b', 1950, 12, 16, '0B', '5012160B'],
    'with single letter uppercase hour' => ['501216Z', 1950, 12, 16, '0Z', '5012160Z'],
    'with numeric hour' => ['50121600', 1950, 12, 16, '00', '50121600'],
    'with 0A suffix hour' => ['4912070A', 1949, 12, 7, '0A', '4912070A'],
    'with 00A suffix hour' => ['49110800A', 1949, 11, 8, '0A', '4911080A'],
    'with 00B suffix hour' => ['49110800B', 1949, 11, 8, '0B', '4911080B'],
    'with 49 year mapped to 1949' => ['49123112', 1949, 12, 31, '12', '49123112'],
    'with 99 year mapped to 1999' => ['99123112', 1999, 12, 31, '12', '99123112'],
    'with 00 year mapped to 2000' => ['00123112', 2000, 12, 31, '12', '00123112'],
    'with 01 year mapped to 2001' => ['01123112', 2001, 12, 31, '12', '01123112'],
    'with 1x year mapped to 20xx' => ['10123112', 2010, 12, 31, '12', '10123112'],
    'with 2x year mapped to 20xx' => ['26123112', 2026, 12, 31, '12', '26123112'],
    'with 3x year mapped to 20xx' => ['30123112', 2030, 12, 31, '12', '30123112'],
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
    $code = new TranscriptCode(year: 2005, month: 1, day: 9, hour: null);

    expect($code->year)->toBe(2005)
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
