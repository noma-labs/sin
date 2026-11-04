<?php

declare(strict_types=1);

namespace Tests\Biblioteca;

use App\Biblioteca\Collocazione;

it('splits a collocazione into colonna, ripiano and numero', function (): void {
    $collocazione = Collocazione::parse('dgb039');

    expect($collocazione->colonna)->toBe('DG')
        ->and($collocazione->ripiano)->toBe('B')
        ->and($collocazione->numero)->toBe(39)
        ->and($collocazione->isElaborato())->toBeFalse();
});

it('reads an elaborato collocazione as a colonna without ripiano', function (): void {
    $collocazione = Collocazione::parse('SPA012');

    expect($collocazione->colonna)->toBe('SPA')
        ->and($collocazione->ripiano)->toBeNull()
        ->and($collocazione->numero)->toBe(12)
        ->and($collocazione->isElaborato())->toBeTrue();
});

it('reads the lettere of a collocazione without the numero', function (): void {
    $collocazione = Collocazione::parse('HFA');

    expect($collocazione->colonna)->toBe('HF')
        ->and($collocazione->ripiano)->toBe('A')
        ->and($collocazione->numero)->toBeNull();
});

it('returns null for an invalid collocazione', function (?string $valore): void {
    expect(Collocazione::parse($valore))->toBeNull();
})->with([null, '', 'NULL01', 'Illo.69', 'AB12', 'ABCD001']);
