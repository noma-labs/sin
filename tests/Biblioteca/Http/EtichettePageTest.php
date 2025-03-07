<?php

declare(strict_types=1);

namespace Tests\Biblioteca\Feature;

use App\Biblioteca\Models\Libro;

it('download the etichette pfd of multiple books', function (): void {

    Libro::factory()
        ->toBePrinted()
        ->count(5)
        ->create();

    login();

    $this
        ->get(route('books.labels.print'))
        ->assertSuccessful()
        ->assertDownload();

});

it('download the etichette pfd of a single book', function (): void {

    $libro = Libro::factory()
        ->toBePrinted()
        ->create();

    login();

    $this
        ->get(route('books.labels.print', ['idLibro' => $libro->id]))
        ->assertSuccessful()
        ->assertDownload();

});
