<?php

namespace Tests\Scuola\Unit;

use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Models\Studente;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;

it('add responsible to school', function (): void {
    $a = Anno::createAnno(2014);
    $p = Studente::factory()->maggiorenne()->maschio()->create();
    $a->aggiungiResponsabile($p);
    expect($a->responsabile->id)->toBe($p->id);
});

it('build new year correctly', function (): void {
    $a = Anno::createAnno(1999);
    expect($a->scolastico)->toBe('1999/2000')
        ->and($a->nextAnnoScolasticoString())->toBe('2000/2001');
});

it('add classroom', function (): void {
    $a = Anno::createAnno(2007);
    expect($a->id)->not->toBeNull();
    $tipi = ClasseTipo::all();
    $t = $tipi->random();
    expect($a->classi()->count())->toBe(0)
        ->and(count($a->classiTipoPossibili()))->toBe(count($tipi));
    $c = $a->aggiungiClasse($t);
    expect(count($a->classiTipoPossibili()))->toBe(count($tipi) - 1)
        ->and($a->classi()->count())->toBe(1)
        ->and($c->alunni()->count())->toBe(0)
        ->and($c->anno->id)->toBe($a->id);
    $p1 = Studente::factory()->minorenne()->maschio()->create();
    $c->aggiungiAlunno($p1, Carbon::now());
    expect($c->alunni()->count())->toBe(1);
});

it('get student from year', function (): void {
    $a = Anno::createAnno(2018);
    $t = ClasseTipo::all();

    // aggiuni 3 classi nell'anno e controlla
    $c1 = $a->aggiungiClasse($t->get(0));
    $p1 = Studente::factory()->minorenne()->maschio()->create();
    $c1->aggiungiAlunno($p1, Carbon::now());

    $c2 = $a->aggiungiClasse($t->get(2));
    $p2 = Studente::factory()->minorenne()->maschio()->create();
    $c2->aggiungiAlunno($p2, Carbon::now());

    $c3 = $a->aggiungiClasse($t->get(3));
    $p3 = Studente::factory()->minorenne()->femmina()->create();
    $c3->aggiungiAlunno($p3, Carbon::now());

    expect($a->classi()->count())->toBe(3)
        ->and(Studente::InAnnoScolastico($a->id)->count())->toBe(3)
        ->and(Studente::InAnnoScolastico($a)->count())->toBe(3)
        ->and($c3->alunni()->where('nominativo', $p3->nominativo))->not->toBeEmpty()
        ->and($p3->isDeceduto())->toBeFalse();

});

it('get students from classroom types', function (): void {
    $a = Anno::createAnno(1815);
    $t = ClasseTipo::all();
    $now = Carbon::now();

    // prescuola
    $c1 = $a->aggiungiClasse($t->get(0));
    $p1 = Studente::factory()->minorenne()->maschio()->nato($now)->create();
    $c1->aggiungiAlunno($p1, Carbon::now());

    // elemenatri
    $c2 = $a->aggiungiClasse($t->get(3));
    $p2 = Studente::factory()->minorenne()->maschio()->nato($now->addYears(1))->create();
    $c2->aggiungiAlunno($p2, Carbon::now());

    // medie
    $c3 = $a->aggiungiClasse($t->get(10));
    $p3 = Studente::factory()->minorenne()->femmina()->nato($now->addYears(2))->create();
    $c3->aggiungiAlunno($p3, Carbon::now());
    $p4 = Studente::factory()->minorenne()->femmina()->nato($now->addYears(3))->create();
    $c3->aggiungiAlunno($p4, Carbon::now());

    $tot = Studente::InAnnoScolasticoPerCiclo($a)->get();
    expect(count($tot))->toBe(3)
        ->and($tot[0]->count)->toBe(1)
        ->and($tot[1]->count)->toBe(1)
        ->and($tot[2]->count)->toBe(2);
});

it('create classroom in a year', function (): void {

    $a = Anno::createAnno(2019);

    $t = ClasseTipo::all();
    $this->assertGreaterThan(0, $t->count());

    // aggiuni una classe in un anno scolastico
    $tipo = $t->random();
    $classe = $a->aggiungiClasse($tipo);
    $this->assertNotEmpty($classe->id);
    $persona = Studente::factory()->minorenne()->maschio()->create();

    expect($classe->alunni()->count())->toBe(0);
    $classe->aggiungiAlunno($persona, Carbon::now());
    expect($classe->alunni()->count())->toBe(1);
    expect($classe->tipo->id)->toBe($tipo->id);
});

it('get prescuola type', function (): void {
    $t = ClasseTipo::findOrFail(1);
    expect($t->isPrescuola())->toBeTrue();
});

it('add new student', function (): void {
    $now = Carbon::now();
    $a = Anno::createAnno(2002, $now);
    expect($a->data_inizio->toDateString())->toBe($now->toDateString());
    $c = $a->aggiungiClasse(ClasseTipo::all()->random());
    $p1 = Studente::factory()->minorenne()->maschio()->create();

    // Add alunno with a carbon
    $c->aggiungiAlunno($p1, $now->addDays(15));
});

it('add teacher', function (): void {
    $now = Carbon::now();
    $a = Anno::createAnno(2199, $now);

    $prescuola = ClasseTipo::prescuola()->first();
    $c = $a->aggiungiClasse($prescuola);

    $c->aggiungiCoordinatore(Persona::factory()->maggiorenne()->maschio()->create(), $now->addDays(15));
    expect($c->coordinatori()->count())->toBe(1);
    $c->aggiungiCoordinatore(Persona::factory()->maggiorenne()->maschio()->create(), $now->addDays(15));

    $r = $a->coordinatoriPrescuola();
    expect(count($r[ClasseTipo::PRESCUOLA_3ANNI]))->toBe(2);
});

it('get next type of classroom', function (): void {

    $a = Anno::createAnno(2030, '2023-12-12', true);
    $this->assertEquals($a->prescuola3Anni()->nextClasseTipo(), ClasseTipo::Anni4Prescuola());
    $this->assertEquals($a->prescuola4Anni()->nextClasseTipo(), ClasseTipo::Anni5Prescuola());
    $this->assertEquals($a->prescuola5Anni()->nextClasseTipo(), ClasseTipo::PrimaElem());
    $this->assertEquals($a->primaElementare()->nextClasseTipo(), ClasseTipo::SecondaElem());
    $this->assertEquals($a->secondaElementare()->nextClasseTipo(), ClasseTipo::TerzaElem());
    $this->assertEquals($a->terzaElementare()->nextClasseTipo(), ClasseTipo::QuartaElem());
    $this->assertEquals($a->quintaElementare()->nextClasseTipo(), ClasseTipo::PrimaMed());

    $this->assertEquals($a->primaMedia()->nextClasseTipo(), ClasseTipo::SecondaMed());
    $this->assertEquals($a->secondaMedia()->nextClasseTipo(), ClasseTipo::TerzaMed());
});

it('clone students from existing year', function (): void {
    $a = Anno::createAnno(2050, '2023-12-12', true);
    $this->assertCount(12, $a->classi()->get());

    $a->prescuola()->aggiungiAlunno(Studente::factory()->diEta(5)->maschio()->create(), Carbon::now());
    $a->prescuola()->aggiungiAlunno(Studente::factory()->diEta(3)->maschio()->create(), Carbon::now());
    $a->primaElementare()->aggiungiAlunno(Studente::factory()->diEta(6)->maschio()->create(), Carbon::now());
    $a->secondaElementare()->aggiungiAlunno(Studente::factory()->diEta(7)->maschio()->create(), Carbon::now());
    $a->terzaElementare()->aggiungiAlunno(Studente::factory()->diEta(8)->maschio()->create(), Carbon::now());
    $a->quartaElementare()->aggiungiAlunno(Studente::factory()->diEta(9)->maschio()->create(), Carbon::now());
    $a->quintaElementare()->aggiungiAlunno(Studente::factory()->diEta(10)->maschio()->create(), Carbon::now());
    $a->primaMedia()->aggiungiAlunno(Studente::factory()->diEta(11)->maschio()->create(), Carbon::now());
    $a->secondaMedia()->aggiungiAlunno(Studente::factory()->diEta(12)->maschio()->create(), Carbon::now());
    $a->terzaMedia()->aggiungiAlunno(Studente::factory()->diEta(13)->maschio()->create(), Carbon::now());

    expect($a->prescuola()->alunni()->get())->toHaveCount(2);
    expect($a->alunni())->toHaveCount(10);
    //TODO: sistemare findOrCreateClasseByTipo() must be an instance of App\Scuola\Models\ClasseTipo, instance of Illuminate\Database\Eloquent\Builder given
    //        $aNew = Anno::cloneAnnoScolastico($a, '2024-08-01');
    //        $this->assertEquals('2024-08-01', $aNew->data inizio);
    //        $this->assertCount(11, $aNew->classi()->get());
    //        $this->assertCount(10, $aNew->alunni());

});

it('copy students from other classroom', function (): void {
    $a = Anno::createAnno(2034, '2023-12-12', true);
    expect($a->classi()->get())->toHaveCount(12);

    $pre = $a->prescuola();
    $pre->aggiungiAlunno(Studente::factory()->diEta(5)->maschio()->create(), Carbon::now());
    $pre->aggiungiAlunno(Studente::factory()->diEta(3)->maschio()->create(), Carbon::now());
    expect($pre->alunni()->get())->toHaveCount(2);

    $aNew = Anno::createAnno(2035, '2024-08-01', true);
    $aNew->primaElementare()->importStudentsFromOtherClasse($pre, '2024-08-01');
    expect($aNew->primaElementare()->alunni()->get())->toHaveCount(2);
    expect($aNew->alunni())->toHaveCount(2);
});

it('get next classroom', function (): void {
    $a = Anno::createAnno(2037, '2023-12-12', true);
    $this->assertEquals($a->prescuola5Anni()->nextClasseTipo(), ClasseTipo::PrimaElem());
    $this->assertEquals($a->primaElementare()->nextClasseTipo(), ClasseTipo::SecondaElem());
    $this->assertEquals($a->secondaElementare()->nextClasseTipo(), ClasseTipo::TerzaElem());
    $this->assertEquals($a->terzaElementare()->nextClasseTipo(), ClasseTipo::QuartaElem());
    $this->assertEquals($a->quintaElementare()->nextClasseTipo(), ClasseTipo::PrimaMed());

    $this->assertEquals($a->primaMedia()->nextClasseTipo(), ClasseTipo::SecondaMed());
    $this->assertEquals($a->secondaMedia()->nextClasseTipo(), ClasseTipo::TerzaMed());
});

it('first or create classroom', function (): void {
    $a = Anno::createAnno(2070, '2023-12-12');
    expect($a->classi()->get())->toHaveCount(0);
    $a->findOrCreateClasseByTipo(ClasseTipo::prescuola());
    expect($a->classi()->get())->toHaveCount(1);

});

it('get possible students in year', function (): void {
    $anno = 1994;
    $a = Anno::createAnno($anno, '2023-12-12', true);
    expect($a->classi()->get())->toHaveCount(12);
    $alunno = Studente::factory()->nato(Carbon::parse('1990-01-01'))->maschio()->create();
    $alunnoFem = Studente::factory()->nato(Carbon::parse('1990-12-31'))->femmina()->create();

    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($capoFam);
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, Carbon::now()->toDateString(), $gruppo);

    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($alunno, Famiglia::findOrFail($famiglia->id));

    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($alunnoFem, Famiglia::findOrFail($famiglia->id));

    //    $this->assertEquals(2, Studente::FraEta(3, 6, 'nominativo', $anno, true)->count());
    expect($a->prescuola()->alunniPossibili()->count())->toBe(2);
    $a->prescuola()->aggiungiAlunno($alunno, Carbon::now());
    expect($a->prescuola()->alunniPossibili()->count())->toBe(1);
});
