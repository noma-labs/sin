<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Persona;
use App\Scuola\Exceptions\CouldNotAssignAlunno;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Carbon\Carbon;
use Tests\TestCase;


class ScuolaTest extends TestCase
{

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
        $p1 = factory(Persona::class)->states("minorenne", "maschio")->create();
        $c->aggiungiAlunno($p1, Carbon::now());
        $this->assertCount(1, $c->alunni()->get());

    }

    public function testGetAlunnniInAnno()
    {
        $a = Anno::createAnno(2018);
        $t = ClasseTipo::all();

        // aggiuni due classi nell'anno e controlla
        $c1 = $a->aggiungiClasse($t->get(0));
        $p1 = factory(Persona::class)->states("minorenne", "maschio")->create();
        $c1->aggiungiAlunno($p1, Carbon::now());

        $c2 = $a->aggiungiClasse($t->get(2));
        $p2 = factory(Persona::class)->states("minorenne", "maschio")->create();
        $c2->aggiungiAlunno($p2, Carbon::now());

        $c3 = $a->aggiungiClasse($t->get(3));
        $p3 = factory(Persona::class)->states("minorenne", "femmina")->create();
        $c3->aggiungiAlunno($p3, Carbon::now());

        $this->assertCount(3, $a->classi()->get());
        $this->assertCount(3, $a->alunni());

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
        $persona = factory(Persona::class)->states("minorenne", "maschio")->create();

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
}
