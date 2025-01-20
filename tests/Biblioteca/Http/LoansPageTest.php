<?php

declare(strict_types=1);

namespace Tests\Biblioteca\Feature;

use App\Biblioteca\Controllers\BooksCallNumberController;
use App\Biblioteca\Controllers\BooksController;
use App\Biblioteca\Controllers\LoansController;
use App\Biblioteca\Controllers\SearchableBooksController;
use App\Biblioteca\Models\Autore;
use App\Biblioteca\Models\Classificazione;
use App\Biblioteca\Models\Editore;
use App\Biblioteca\Models\Libro;

use function Pest\Laravel\post;
use function Pest\Laravel\put;

it('shows loans page to logged user', function (): void {
    login();

    $this
        ->get(action([LoansController::class, 'index']))
        ->assertSuccessful()
        ->assertSee('Gestione prestiti');
});
