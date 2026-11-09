<?php

declare(strict_types=1);

namespace Tests\Http\Scuola;

use App\Scuola\Controllers\ElaboratiArgomentiController;
use App\Scuola\Models\ArgomentoElaborato;
use App\Scuola\Models\Elaborato;

it('adds an argomento with its materia to an elaborato', function (): void {
    $elaborato = Elaborato::factory()->create();

    login();

    $this->post(action([ElaboratiArgomentiController::class, 'store'], $elaborato->id), [
        'argomento' => ' Il Risorgimento ',
        'materia' => 'Storia',
    ])->assertRedirectToRoute('scuola.elaborati.argomenti.create', $elaborato->id);

    expect($elaborato->argomenti()->get(['argomento', 'materia'])->toArray())
        ->toBe([['argomento' => 'Il Risorgimento', 'materia' => 'Storia']]);
});

it('adds an argomento without materia', function (): void {
    $elaborato = Elaborato::factory()->create();

    login();

    $this->post(action([ElaboratiArgomentiController::class, 'store'], $elaborato->id), [
        'argomento' => 'Gita a Roma',
        'materia' => '',
    ])->assertRedirect();

    expect($elaborato->argomenti()->sole()->materia)->toBeNull();
});

it('does not add the same argomento twice to an elaborato', function (?string $materia): void {
    $elaborato = Elaborato::factory()->create();
    ArgomentoElaborato::factory()->for($elaborato)->create(['argomento' => 'La fotosintesi', 'materia' => $materia]);

    login();

    $this->post(action([ElaboratiArgomentiController::class, 'store'], $elaborato->id), [
        'argomento' => 'La fotosintesi',
        'materia' => $materia,
    ])->assertRedirect();

    expect($elaborato->argomenti()->count())->toBe(1);
})->with([
    'with materia' => ['Scienze'],
    'without materia' => [null],
]);

it('rejects an argomento without text', function (): void {
    $elaborato = Elaborato::factory()->create();

    login();

    $this->post(action([ElaboratiArgomentiController::class, 'store'], $elaborato->id), [
        'argomento' => '',
        'materia' => 'Storia',
    ])->assertSessionHasErrors(['argomento' => "L'argomento è obbligatorio."]);

    expect($elaborato->argomenti()->exists())->toBeFalse();
});

it('removes an argomento from an elaborato', function (): void {
    $argomento = ArgomentoElaborato::factory()->create();

    login();

    $this->delete(action([ElaboratiArgomentiController::class, 'destroy'], ['id' => $argomento->elaborato_id, 'argomentoId' => $argomento->id]))
        ->assertRedirectToRoute('scuola.elaborati.argomenti.create', $argomento->elaborato_id);

    $this->assertModelMissing($argomento);
});

it('does not remove the argomento of another elaborato', function (): void {
    $argomento = ArgomentoElaborato::factory()->create();
    $altroElaborato = Elaborato::factory()->create();

    login();

    $this->delete(action([ElaboratiArgomentiController::class, 'destroy'], ['id' => $altroElaborato->id, 'argomentoId' => $argomento->id]))
        ->assertNotFound();

    $this->assertModelExists($argomento);
});

it('suggests the argomenti and the materie already used, each once', function (): void {
    ArgomentoElaborato::factory()->create(['argomento' => 'Le piante', 'materia' => 'Scienze']);
    ArgomentoElaborato::factory()->create(['argomento' => 'Le piante', 'materia' => 'Scienze']);
    $elaborato = Elaborato::factory()->create();

    login();

    $response = $this->get(action([ElaboratiArgomentiController::class, 'create'], $elaborato->id))
        ->assertOk()
        ->assertSee('Nessun argomento.');

    $pagina = (string) $response->getContent();
    expect(mb_substr_count($pagina, '<option value="Le piante"></option>'))->toBe(1)
        ->and(mb_substr_count($pagina, '<option value="Scienze"></option>'))->toBe(1);
});
