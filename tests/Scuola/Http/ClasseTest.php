<?php


declare(strict_types=1);

namespace Tests\Http\Scuola;

use App\Scuola\Controllers\ClassiController;
use App\Scuola\Controllers\ClassiTipoController;
use App\Scuola\Controllers\ScuolaController;
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

    $this->get(action([ScuolaController::class, 'show'], ['id' => $a->id]))
        ->assertSuccessful()
        ->assertDontSee($s->nominativo);
});

it('update the tipo of a classe', function (): void {
    $a = Anno::createAnno(2000);
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
