<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Livewire\FilterAlunno;
use App\Scuola\Controllers\ElaboratiStudentsController;
use App\Scuola\Models\Anno;
use App\Scuola\Models\Elaborato;
use App\Scuola\Models\Studente;
use Livewire\Livewire;

it('asks to choose an anno when the scolastico has no anno', function (?string $scolastico): void {
    Livewire::test(FilterAlunno::class, ['scolastico' => $scolastico])
        ->assertOk()
        ->assertSet('selectedAnnoScolastico', null)
        ->assertSee('Seleziona anno scolastico');
})->with([
    'unknown' => ['1900/1901'],
    'empty' => [''],
    'missing' => [null],
]);

it('selects the anno matching the scolastico', function (): void {
    $anno = Anno::createAnno(2090);

    $component = Livewire::test(FilterAlunno::class, ['scolastico' => '2090/2091'])
        ->assertOk()
        ->assertDontSee('Seleziona anno scolastico');

    expect($component->get('selectedAnnoScolastico')->id)->toBe($anno->id);
});

it('selects an anno after starting without one', function (): void {
    $anno = Anno::createAnno(2091);

    $component = Livewire::test(FilterAlunno::class, ['scolastico' => '1900/1901'])
        ->call('selectAnnoScolastico', $anno->id)
        ->assertOk();

    expect($component->get('selectedAnnoScolastico')->id)->toBe($anno->id);
});

it('can clear the filters when no anno is selected', function (): void {
    Livewire::test(FilterAlunno::class, ['scolastico' => '1900/1901'])
        ->call('toggleSelectAll', 'on')
        ->assertOk()
        ->assertSet('students', []);
});

it('shows the students page of an elaborato whose anno scolastico has no anno', function (): void {
    $elaborato = Elaborato::factory()->create(['anno_scolastico' => '1900/1901']);

    login();

    $this->get(action([ElaboratiStudentsController::class, 'create'], $elaborato->id))
        ->assertOk();
});

it('keeps the students of an elaborato when none is submitted', function (): void {
    $studente = Studente::factory()->minorenne()->maschio()->create();
    $elaborato = Elaborato::factory()->create();
    $elaborato->studenti()->attach($studente->id);

    login();

    $this->post(action([ElaboratiStudentsController::class, 'store'], $elaborato->id))
        ->assertSessionHasErrors(['students' => 'Seleziona almeno uno studente.']);

    expect($elaborato->studenti()->pluck('id')->all())->toBe([$studente->id]);
});

it('sets the submitted students on an elaborato', function (): void {
    $studente = Studente::factory()->minorenne()->maschio()->create();
    $elaborato = Elaborato::factory()->create();

    login();

    $this->post(action([ElaboratiStudentsController::class, 'store'], $elaborato->id), [
        'students' => [$studente->id],
    ])->assertRedirectToRoute('scuola.elaborati.show', $elaborato->id);

    expect($elaborato->studenti()->pluck('id')->all())->toBe([$studente->id]);
});
