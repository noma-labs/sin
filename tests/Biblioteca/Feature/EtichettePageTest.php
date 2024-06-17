<?php

namespace Tests\Biblioteca\Feature;

use App\Biblioteca\Models\Libro;

it('download the etichette pfd of multiple books', function (): void {

    Libro::factory()
        ->toBePrinted()
        ->count(5)
        ->create();

    login();

    $this
        ->get(route('libri.etichette.stampa'))
        ->assertSuccessful()
        ->assertDownload();

})->skip('action has problem with puppeteer');

it('download the etichette pfd of a single book', function (): void {

    $libro = Libro::factory()
        ->toBePrinted()
        ->create();

    login();

    $this
        ->get(route('libri.etichette.stampa', ['idLibro' => $libro->id]))
        ->assertSuccessful()
        ->assertDownload();

})->skip('action has problem with puppeteer');
