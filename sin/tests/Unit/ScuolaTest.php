<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Persona;
use App\Scuola\Exceptions\CouldNotAssignAlunno;
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
        $p = Persona::factory()->maggiorenne()->maschio()->create();
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
        $p1 = Persona::factory()->minorenne()->maschio()->create();
        $c->aggiungiAlunno($p1, Carbon::now());
        $this->assertCount(1, $c->alunni()->get());

    }

    public function testGetAlunnniInAnno()
    {
        $a = Anno::createAnno(2018);
        $t = ClasseTipo::all();

        // aggiuni due classi nell'anno e controlla
        $c1 = $a->aggiungiClasse($t->get(0));
        $p1 = Persona::factory()->minorenne()->maschio()->create();
        $c1->aggiungiAlunno($p1, Carbon::now());

        $c2 = $a->aggiungiClasse($t->get(2));
        $p2 = Persona::factory()->minorenne()->maschio()->create();
        $c2->aggiungiAlunno($p2, Carbon::now());

        $c3 = $a->aggiungiClasse($t->get(3));
        $p3 = Persona::factory()->minorenne()->femmina()->create();
        $c3->aggiungiAlunno($p3, Carbon::now());

        $this->assertCount(3, $a->classi()->get());
        $this->assertEquals(3, Studente::InAnnoScolastico($a)->count());
        $this->assertEquals(3, Studente::InAnnoScolastico($a->id)->count());
        $this->assertEquals(3, Studente::InAnnoScolastico($a)->count());

    }

    public function testGetAlunnniPerCicloInAnno()
    {
        $a = Anno::createAnno(1991);
        $t = ClasseTipo::all();

        // prescuola
        $c1 = $a->aggiungiClasse($t->get(0));
        $p1 = Persona::factory()->minorenne()->maschio()->create();
        $c1->aggiungiAlunno($p1, Carbon::now());

        // elemenatri
        $c2 = $a->aggiungiClasse($t->get(2));
        $p2 = Persona::factory()->minorenne()->maschio()->create();
        $c2->aggiungiAlunno($p2, Carbon::now());

        // medie
        $c3 = $a->aggiungiClasse($t->get(6));
        $p3 = Persona::factory()->minorenne()->femmina()->create();
        $c3->aggiungiAlunno($p3, Carbon::now());
        $p4 = Persona::factory()->minorenne()->femmina()->create();
        $c3->aggiungiAlunno($p4, Carbon::now());

        $tot =  Studente::InAnnoScolasticoPerCiclo($a)->get();
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
        $persona = Persona::factory()->minorenne()->maschio()->create();

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
        $p1 = Persona::factory()->minorenne()->maschio()->create();

        // Add alunno with a carbon
        $c->aggiungiAlunno($p1, $now->addDays(15));
    }

    /** @test  **/
    public function add_coordinatore_with_data_inizio()
    {
        $now = Carbon::now();
        $a = Anno::createAnno(2199, $now);
        $this->assertEquals($now->toDateString(), $a->data_inizio->toDateString());
        $c = $a->aggiungiClasse(ClasseTipo::all()->first());
        $p1 = Persona::factory()->maggiorenne()->maschio()->create();

        // Add coordinatore with a carbon
        $c->aggiungiCoordinatore($p1, $now->addDays(15));
        $this->assertCount(1, $c->coordinatori()->get());

        $p1 = Persona::factory()->maggiorenne()->maschio()->create();
        $c->aggiungiCoordinatore($p1, $now->addDays(15));

        $r = $a->coordinatoriPrescuola();
        $this->assertCount(2, $r['Prescuola']);
    }
}
