<?php

namespace Tests\Biblioteca\Feature;

use App\Biblioteca\Models\Libro;

it('download the etichette pfd of multiple books', function () {

    Libro::factory()
        ->toBePrinted()
        ->count(5)
        ->create();

    login();

    $this
        ->get(route('libri.etichette.stampa'))
        ->assertSuccessful()
        ->assertDownload();

})->only();

it('download the etichette pfd of a single book', function () {

    $libro = Libro::factory()
        ->toBePrinted()
        ->create();

    login();

    $this
        ->get(route('libri.etichette.stampa', ['idLibro' => $libro->id]))
        ->assertSuccessful()
        ->assertDownload();

})->only();
