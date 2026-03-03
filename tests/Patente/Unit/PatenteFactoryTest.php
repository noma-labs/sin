<?php

declare(strict_types=1);

use App\Patente\Models\Patente;

it('creates a patente with valid attributes', function (): void {
    $patente = Patente::factory()->make();

    expect($patente->numero_patente)->not->toBeEmpty();
    expect($patente->persona_id)->not->toBeNull();
    expect($patente->data_rilascio_patente)->not->toBeEmpty();
    expect($patente->rilasciata_dal)->not->toBeEmpty();
    expect($patente->data_scadenza_patente)->not->toBeEmpty();
    expect($patente->stato)->not->toBeEmpty();
})->only();
