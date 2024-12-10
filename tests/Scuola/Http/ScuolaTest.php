<?php

declare(strict_types=1);

namespace Tests\Http\Scuola;

use App\Scuola\Controllers\AnnoScolasticoController;
use App\Scuola\Controllers\ScuolaController;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Models\Studente;
use Carbon\Carbon;

it('forbids access to guests', function (): void {
    $this->get(action([ScuolaController::class, 'summary']))
        ->assertRedirect(route('login'));

});

it('can list anni scolastici', function (): void {
    login();
    $this->get(action([ScuolaController::class, 'summary']))
        ->assertSuccessful();

});

it('can create nuovo anno', function (): void {
    login();
    $this->withoutExceptionHandling();
    $this->post(action([AnnoScolasticoController::class, 'store'], [
        'anno inizio' => '2023',
        'data inizio' => '2023-12-12',
    ]))->assertRedirect();

    $this->assertDatabaseHas('db_scuola.anno', [
        'scolastico' => '2023/2024',
    ]);
});

it('can list classi in anno scolastico', function (): void {
    $a = Anno::createAnno(2017);
    $tipi = ClasseTipo::all();
    $c = $a->aggiungiClasse($tipi->random());
    $c->aggiungiAlunno(Studente::factory()->minorenne()->maschio()->create(), Carbon::now());
    $this->assertCount(1, $c->alunni()->get());

    login();
    $this->get(action([AnnoScolasticoController::class, 'show'], ['id' => $a->id]))
        ->assertSuccessful();

});
