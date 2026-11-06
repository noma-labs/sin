<?php

declare(strict_types=1);

namespace Tests\Http\Scuola;

use App\Scuola\Controllers\AnnoScolasticoController;
use App\Scuola\Controllers\ClassiController;
use App\Scuola\Controllers\ClassiTipoController;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Models\Studente;
use Carbon\Carbon;

it('can delete classe', function (): void {
    $a = Anno::createAnno(2000);
    $tipi = ClasseTipo::all();
    $c = $a->aggiungiClasse($tipi->random());
    $s = Studente::factory()->minorenne()->maschio()->create();
    $c->aggiungiAlunno($s, Carbon::now());
    $coord = Studente::factory()->maggiorenne()->maschio()->create();
    $c->aggiungiCoordinatore($coord, Carbon::now());
    $this->assertCount(1, $c->alunni()->get());

    login();

    $this->get(action([ClassiController::class, 'show'], ['id' => $c->id]))
        ->assertSuccessful()
        ->assertSee($s->nominativo)
        ->assertSee($coord->nominativo);

    $this->delete(action([ClassiController::class, 'delete'], ['id' => $c->id]));

    $this->get(action([AnnoScolasticoController::class, 'show'], ['id' => $a->id]))
        ->assertSuccessful()
        ->assertDontSee($s->nominativo);
});

it('update the tipo of a classe', function (): void {
    $a = Anno::createAnno(2040);
    $c = $a->aggiungiClasse(ClasseTipo::first());
    $s = Studente::factory()->minorenne()->maschio()->create();
    $c->aggiungiAlunno($s, Carbon::now());

    login();

    $tipo = ClasseTipo::all()->random();
    $this->put(action([ClassiTipoController::class, 'update'], $c->id), [
        'tipo_id' => $tipo->id,
    ])->assertRedirect();

    $c = $c->refresh();
    expect($c->tipo->id)->toEqual($tipo->id);
});

it('shows alunni and coordinatori by nome and cognome with the nominativo when it differs', function (): void {
    $classe = Anno::createAnno(2061)->aggiungiClasse(ClasseTipo::first());
    $alunno = Studente::factory()->minorenne()->maschio()->create(['nome' => 'Giovanni', 'cognome' => 'Rossetti', 'nominativo' => 'Gianni R.']);
    $classe->aggiungiAlunno($alunno, Carbon::now());
    $coordinatore = Studente::factory()->maggiorenne()->maschio()->create(['nome' => 'Marco', 'cognome' => 'Bianchetti', 'nominativo' => 'Marchino']);
    $classe->aggiungiCoordinatore($coordinatore, Carbon::now());

    login();

    $this->get(action([ClassiController::class, 'show'], ['id' => $classe->id]))
        ->assertSuccessful()
        ->assertSeeTextInOrder(['Giovanni Rossetti', '(Gianni R.)'])
        ->assertSeeTextInOrder(['Marco Bianchetti', '(Marchino)']);
});

it('shows only nome and cognome of an alunno when the nominativo repeats them', function (string $nominativo, int $anno): void {
    $classe = Anno::createAnno($anno)->aggiungiClasse(ClasseTipo::first());
    $alunno = Studente::factory()->minorenne()->maschio()->create(['nome' => 'Giovanni', 'cognome' => 'Rossi', 'nominativo' => $nominativo, 'data_nascita' => fake()->unique()->date()]);
    $classe->aggiungiAlunno($alunno, Carbon::now());

    login();

    $this->get(action([ClassiController::class, 'show'], ['id' => $classe->id]))
        ->assertSuccessful()
        ->assertSeeText('Giovanni Rossi')
        ->assertDontSeeText("($nominativo)");
})->with([
    'nome cognome' => ['Giovanni Rossi', 2062],
    'cognome nome' => ['Rossi Giovanni', 2063],
]);
