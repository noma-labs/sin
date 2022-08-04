<?php

namespace Tests\Unit;

use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use App\Scuola\Models\Studente;
use Carbon\Carbon;
use Tests\TestCase;


class ScuolaTest extends TestCase
{

    public function testAggiungiResponabile()
    {
        $a = Anno::createAnno(2014);
        $p = Studente::factory()->maggiorenne()->maschio()->create();
        $a->aggiungiResponsabile($p);
        $this->assertEquals($p->id, $a->responsabile->id);

    }

    public function testAggiungiClasseInAnno()
    {
        $a = Anno::createAnno(2017);
        $this->assertNotNull($a->id);
        $tipi = ClasseTipo::all();
        $t = $tipi->random();
        $this->assertCount(0, $a->classi()->get());
        $this->assertEquals(count($tipi), count($a->classiTipoPossibili()));
        $c = $a->aggiungiClasse($t);
        $this->assertEquals(count($tipi) - 1, count($a->classiTipoPossibili()));
        $this->assertCount(1, $a->classi()->get());
        $this->assertCount(0, $c->alunni()->get());
        $this->assertEquals($a->id, $c->anno->id);
        $p1 = Studente::factory()->minorenne()->maschio()->create();
        $c->aggiungiAlunno($p1, Carbon::now());
        $this->assertCount(1, $c->alunni()->get());

    }

    public function testGetAlunnniInAnno()
    {
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

        $this->assertCount(3, $a->classi()->get());
        $this->assertEquals(3, Studente::InAnnoScolastico($a->id)->count());
        $this->assertEquals(3, Studente::InAnnoScolastico($a)->count());

        $this->assertNotEmpty($c3->alunni()->where('nominativo', $p3->nominativo));
        $this->assertFalse($p3->isDeceduto());

    }

    public function testGetAlunnniPerCicloInAnno()
    {
        $a = Anno::createAnno(1991);
        $t = ClasseTipo::all();

        // prescuola
        $c1 = $a->aggiungiClasse($t->get(0));
        $p1 = Studente::factory()->minorenne()->maschio()->create();
        $c1->aggiungiAlunno($p1, Carbon::now());

        // elemenatri
        $c2 = $a->aggiungiClasse($t->get(2));
        $p2 = Studente::factory()->minorenne()->maschio()->create();
        $c2->aggiungiAlunno($p2, Carbon::now());

        // medie
        $c3 = $a->aggiungiClasse($t->get(6));
        $p3 = Studente::factory()->minorenne()->femmina()->create();
        $c3->aggiungiAlunno($p3, Carbon::now());
        $p4 = Studente::factory()->minorenne()->femmina()->create();
        $c3->aggiungiAlunno($p4, Carbon::now());

        $tot = Studente::InAnnoScolasticoPerCiclo($a)->get();
        $this->assertCount(3, $tot);
        $this->assertEquals(1, $tot[0]->count);
        $this->assertEquals(1, $tot[1]->count);
        $this->assertEquals(2, $tot[2]->count);
    }

    public function testCreaClasseInAnnoScolastico()
    {
        $a = Anno::createAnno(2019);

        $t = ClasseTipo::all();
        $this->assertGreaterThan(0, $t->count());

        // aggiuni una classe in un anno scolastico
        $tipo = $t->random();
        $classe = $a->aggiungiClasse($tipo);
        $this->assertNotEmpty($classe->id);
        $persona = Studente::factory()->minorenne()->maschio()->create();

        $this->assertCount(0, $classe->alunni()->get());
        $classe->aggiungiAlunno($persona, Carbon::now());
        $this->assertCount(1, $classe->alunni()->get());
        $this->assertEquals($tipo->id, $classe->tipo->id);
    }

    public function testClassiTipo()
    {
        $t = ClasseTipo::findOrFail(1);
        $this->assertTrue($t->isPrescuola());
    }

    public function testAggiungiAlunnoWitDataInizio()
    {
        $now = Carbon::now();
        $a = Anno::createAnno(2002, $now);
        $this->assertEquals($now->toDateString(), $a->data_inizio->toDateString());
        $c = $a->aggiungiClasse(ClasseTipo::all()->random());
        $p1 = Studente::factory()->minorenne()->maschio()->create();

        // Add alunno with a carbon
        $c->aggiungiAlunno($p1, $now->addDays(15));
    }

    /** @test  * */
    public function add_coordinatore_with_data_inizio()
    {
        $now = Carbon::now();
        $a = Anno::createAnno(2199, $now);
        $this->assertEquals($now->toDateString(), $a->data_inizio->toDateString());
        $c = $a->aggiungiClasse(ClasseTipo::all()->first());
        $p1 = Studente::factory()->maggiorenne()->maschio()->create();

        // Add coordinatore with a carbon
        $c->aggiungiCoordinatore($p1, $now->addDays(15));
        $this->assertCount(1, $c->coordinatori()->get());

        $p1 = Studente::factory()->maggiorenne()->maschio()->create();
        $c->aggiungiCoordinatore($p1, $now->addDays(15));

        $r = $a->coordinatoriPrescuola();
        $this->assertCount(2, $r['Prescuola']);
    }

    /** @test */
    public function classe_successiva_is_correct()
    {
        $a = Anno::createAnno(2030, '2023-12-12', true);
        $this->assertEquals($a->prescuola()->nextClasseTipo(), ClasseTipo::PrimaElem());
        $this->assertEquals($a->primaElementare()->nextClasseTipo(), ClasseTipo::SecondaElem());
        $this->assertEquals($a->secondaElementare()->nextClasseTipo(), ClasseTipo::TerzaElem());
        $this->assertEquals($a->terzaElementare()->nextClasseTipo(), ClasseTipo::QuartaElem());
        $this->assertEquals($a->quintaElementare()->nextClasseTipo(), ClasseTipo::PrimaMed());

        $this->assertEquals($a->primaMedia()->nextClasseTipo(), ClasseTipo::SecondaMed());
        $this->assertEquals($a->secondaMedia()->nextClasseTipo(), ClasseTipo::TerzaMed());
//        $this->assertEquals($a->terzaMedia()->tipo->ClasseSuccessiva(), ClasseTipo::SecondaMed());
    }

    /** @test */
    public function copy_students_from_existing_anno()
    {
        $a = Anno::createAnno(2039, "2023-12-12", true);
        $this->assertCount(9, $a->classi()->get());

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

        $this->assertCount(2, $a->prescuola()->alunni()->get());
        $this->assertCount(10, $a->alunni());


        $aNew = Anno::createAnno(2040, '2024-08-01');
        $aNew->importStudentsFromExistingAnno($a);
        $this->assertCount(9, $a->classi()->get());
//        $this->assertCount(10, $aNew->alunni());
    }

    public function copy_students_from_other_classe()
    {
        $a = Anno::createAnno(2034, '2023-12-12', true);
        $this->assertCount(9, $a->classi()->get());

        $pre = $a->prescuola();
        $pre->aggiungiAlunno(Studente::factory()->diEta(5)->maschio()->create(), Carbon::now());
        $pre->aggiungiAlunno(Studente::factory()->diEta(3)->maschio()->create(), Carbon::now());
        $this->assertCount(2, $pre->alunni()->get());

        $aNew = Anno::createAnno(2035, '2024-08-01', true);
        $aNew->primaElementare()->importStudentsFromOtherClasse($pre);
        $this->assertCount(2, $aNew->primaElementare()->alunni()->get());
        $this->assertCount(2, $aNew->alunni()->get());
    }

    public function next_classe_in_anno()
    {
        $a = Anno::createAnno(2035, '2023-12-12', true);
        $this->assertEquals($a->prescuola()->nextClasseTipo(), ClasseTipo::PrimaElem());
        $this->assertEquals($a->primaElementare()->nextClasseTipo(), ClasseTipo::SecondaElem());
        $this->assertEquals($a->secondaElementare()->nextClasseTipo(), ClasseTipo::TerzaElem());
        $this->assertEquals($a->terzaElementare()->nextClasseTipo(), ClasseTipo::QuartaElem());
        $this->assertEquals($a->quintaElementare()->nextClasseTipo(), ClasseTipo::PrimaMed());

        $this->assertEquals($a->primaMedia()->nextClasseTipo(), ClasseTipo::SecondaMed());
        $this->assertEquals($a->secondaMedia()->nextClasseTipo(), ClasseTipo::TerzaMed());
    }

    public function first_or_create_classe_in_anno()
    {
        $a = Anno::createAnno(2035, '2023-12-12');
        $this->assertCount(0, $a->classi());
        $a->findOrCreateClasseByTipo(ClasseTipo::prescuola());
        $this->assertCount(0, $a->classi());

    }
}
