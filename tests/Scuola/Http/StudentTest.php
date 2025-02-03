<?php

declare(strict_types=1);

namespace Tests\Http\Scuola;

use App\Scuola\Controllers\StudentController;
use App\Scuola\Controllers\StudentWorksController;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Models\Elaborato;
use App\Scuola\Models\Studente;
use Carbon\Carbon;

it('can render the student show page', function (): void {
    $student = Studente::factory()->minorenne()->maschio()->create();
    $a = Anno::createAnno(2029);
    $c = $a->aggiungiClasse(ClasseTipo::all()->random());
    $c->aggiungiAlunno($student, Carbon::now());

    $elaborato = Elaborato::factory()->create();
    $elaborato->studenti()->sync($student);

    login();
    $this->get(action([StudentController::class, 'show'], $student->id))
        ->assertStatus(200)
        ->assertSee($student->nome);

    $this->get(action([StudentWorksController::class, 'show'], $student->id))
        ->assertStatus(200)
        ->assertSee( $elaborato->titolo);
});
