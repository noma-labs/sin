<?php

namespace Tests\Biblioteca\Feature;

use App\Biblioteca\Controllers\LibriController;
use App\Biblioteca\Models\Autore;
use App\Biblioteca\Models\Classificazione;
use App\Biblioteca\Models\Editore;
use App\Biblioteca\Models\Libro;

use function Pest\Laravel\post;

it('will search books by location', function (): void {

    $book = Libro::factory()
        ->physicalPlacement('AAA001')
        ->has(Editore::factory()->count(2), 'editori')
        ->has(Autore::factory()->count(3), 'autori')
        ->create();

    $this
        ->get(action([LibriController::class, 'searchConfirm'], [
            'xCollocazione' => 'AAA',
        ]))
        ->assertSee($book->collocazione);
});

it('will update book when the admin is logged in', function (): void {

    $book = Libro::factory()
        ->has(Editore::factory(), 'editori')
        ->has(Autore::factory(), 'autori')
        ->for(Classificazione::factory(), 'classificazione')
        ->create();

    $title = 'New Title';
    $sendRequest = fn () => post(action([LibriController::class, 'editConfirm'], $book->id), [
        'xTitolo' => $title,
        'xClassificazione' => Classificazione::all()->first()->id,
    ]);

    $sendRequest()->assertRedirect(route('login'));

    login();

    $sendRequest()->assertRedirectToRoute('libro.dettaglio', $book->id);

    // NOTE: the title of the book is converted into upper case when it is inserted into db
    expect(Libro::find($book->id)->titolo)->toBe(strtoupper($title));
});

it('will edit the physical location when the admin is logged in', function (): void {

    $book = Libro::factory()
        ->physicalPlacement('AAA001')
        ->create();

    $new = 'AAA002';
    $sendRequest = fn () => post(action([LibriController::class, 'updateCollocazione'], $book->id), [
        'xCollocazione' => $new,
    ]);

    $sendRequest()->assertRedirect(route('login'));

    login();

    $sendRequest()->assertRedirectToRoute('libro.dettaglio', $book->id);

    expect(Libro::find($book->id)->collocazione)->toBe($new);
});

it('will swap the physical location of two books when the admin is logged in', function (): void {

    $book1 = Libro::factory()
        ->physicalPlacement('AAA099')
        ->create();

    $book2 = Libro::factory()
        ->physicalPlacement('AAA100')
        ->create();

    $sendRequest = fn () => post(action([LibriController::class, 'confirmCollocazione'], $book1->id), [
        'idTarget' => $book2->id,
    ]);

    $sendRequest()->assertRedirect(route('login'));

    login();

    $sendRequest()->assertRedirectToRoute('libro.dettaglio', $book1->id);

    expect(Libro::find($book1->id)->collocazione)->toBe($book2->collocazione);
    expect(Libro::find($book2->id)->collocazione)->toBe($book1->collocazione);
});

it('will insert a book when the admin is logged in', function (): void {

    $sendRequest = fn () => post(action([LibriController::class, 'insertConfirm']), [
        'xTitolo' => 'MY title',
        'xIdAutori' => Autore::factory()->create()->id,
        'xIdEditori' => Editore::factory()->create()->id,
        'xCollocazione' => 'AAA005',
        'xClassificazione' => Classificazione::factory()->create()->id,
    ]);

    $sendRequest()->assertRedirect(route('login'));

    login();

    $sendRequest()->assertOk();

    expect(Libro::where('collocazione', '=', 'AAA005')->get()->count())->toBe(1);

});
