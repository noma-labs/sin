<?php

declare(strict_types=1);

namespace Tests\Biblioteca\Feature;

use App\Biblioteca\Controllers\LibriCollocazioneController;
use App\Biblioteca\Controllers\LibriController;
use App\Biblioteca\Controllers\SearchableLibriController;
use App\Biblioteca\Models\Autore;
use App\Biblioteca\Models\Classificazione;
use App\Biblioteca\Models\Editore;
use App\Biblioteca\Models\Libro;

use function Pest\Laravel\post;
use function Pest\Laravel\put;

it('guest user can load search page homepage', function (): void {
    $this
        ->get(action([SearchableLibriController::class, 'index']))
        ->assertSee('Ricerca Libro');
});

it('will search books by location', function (): void {

    $book = Libro::factory()
        ->physicalPlacement('AAA001')
        ->has(Editore::factory()->count(2), 'editori')
        ->has(Autore::factory()->count(3), 'autori')
        ->create();

    $this
        ->get(action([SearchableLibriController::class, 'search'], [
            'xCollocazione' => 'AAA',
        ]))
        ->assertSee($book->collocazione);
});

it('inserts a book when the admin is logged in', function (): void {
    $sendRequest = fn () => post(action([LibriController::class, 'store']), [
        'xTitolo' => 'MY title',
        'xIdAutori' => Autore::factory()->create()->id,
        'xIdEditori' => Editore::factory()->create()->id,
        'xCollocazione' => 'AAA005',
        'xClassificazione' => Classificazione::factory()->create()->id,
    ]);

    $sendRequest()->assertRedirect(route('login'));

    login();

    $sendRequest()->assertRedirect();

    expect(Libro::where('collocazione', '=', 'AAA005')->get()->count())->toBe(1);

});

it('shows a book detail page', function (): void {
    $book = Libro::factory()
        ->physicalPlacement('BBB001')
        ->has(Editore::factory()->count(2), 'editori')
        ->has(Autore::factory()->count(3), 'autori')
        ->create();

    login();

    $this->get(action([LibriController::class, 'show'], $book->id))
        ->assertSuccessful()
        ->assertSee('BBB001');

});

it('updates a book when the admin is logged in', function (): void {

    $book = Libro::factory()
        ->has(Editore::factory(), 'editori')
        ->has(Autore::factory(), 'autori')
        ->for(Classificazione::factory(), 'classificazione')
        ->create();

    $title = 'New Title';
    $sendRequest = fn () => put(action([LibriController::class, 'update'], $book->id), [
        'xTitolo' => $title,
        'xClassificazione' => Classificazione::all()->first()->id,
    ]);

    $sendRequest()->assertRedirect(route('login'));

    login();

    $sendRequest()->assertRedirect(route('books.show', ['id' => $book->id]));

    // NOTE: the title of the book is converted into upper case when it is inserted into db
    expect(Libro::find($book->id)->titolo)->toEqual(mb_strtoupper($title));
});

it('will edit the call-number when the admin is logged in', function (): void {

    $book = Libro::factory()
        ->physicalPlacement('AAA001')
        ->create();

    $new = 'AAA002';
    $sendRequest = fn () => put(action([LibriCollocazioneController::class, 'update'], $book->id), [
        'xCollocazione' => $new,
    ]);

    $sendRequest()->assertRedirect(route('login'));

    login();

    $sendRequest()->assertRedirectToRoute('books.show', $book->id);

    expect(Libro::find($book->id)->collocazione)->toBe($new);
});

it('will swap the call-number of two books when the admin is logged in', function (): void {

    $book1 = Libro::factory()
        ->physicalPlacement('AAA099')
        ->create();

    $book2 = Libro::factory()
        ->physicalPlacement('AAA100')
        ->create();

    $sendRequest = fn () => put(action('books.call-number.swap.update', ['id' => $book1->id, 'idTarget' => $book2->id]), [
    ]);

    $sendRequest()->assertRedirect(route('login'));

    login();

    $sendRequest()->assertRedirectToRoute('books.show', $book1->id);

    expect(Libro::find($book1->id)->collocazione)->toBe($book2->collocazione);
    expect(Libro::find($book2->id)->collocazione)->toBe($book1->collocazione);
});
