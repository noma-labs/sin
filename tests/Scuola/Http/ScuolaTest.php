<?php

namespace Tests\Http\Scuola;

use App\Scuola\Controllers\ClassiController;
use App\Scuola\Controllers\ScuolaController;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Models\Studente;
use Carbon\Carbon;

it('forbids access to guests', function () {
    $this->get(action([ScuolaController::class, 'summary']))
        ->assertRedirect(route('login'));

});

it('can list anni scolastici', function () {
    login();
    $this->get(action([ScuolaController::class, 'summary']))
        ->assertSuccessful();

});

it('can create nuovo anno', function () {
    login();
    $this->withoutExceptionHandling();
    $this->post(action([ScuolaController::class, 'aggiungiAnnoScolastico'], [
        'anno inizio' => '2023',
        'data inizio' => '2023-12-12',
    ]))->assertRedirect();

    $this->assertDatabaseHas('db_scuola.anno', [
        'scolastico' => '2023/2024',
    ]);
});

it('can list classi in anno scolastico', function () {
    $a = Anno::createAnno(2017);
    $tipi = ClasseTipo::all();
    $c = $a->aggiungiClasse($tipi->random());
    $c->aggiungiAlunno(Studente::factory()->minorenne()->maschio()->create(), Carbon::now());
    $this->assertCount(1, $c->alunni()->get());

    login();
    $this->get(action([ScuolaController::class, 'index'], ['id' => $a->id]))
        ->assertSuccessful();

});

it('can delete classe', function () {
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

    $this->get(action([ScuolaController::class, 'index'], ['id' => $a->id]))
        ->assertSuccessful()
        ->assertDontSee($s->nominativo);
});
