<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Persona;
use App\Scuola\Exceptions\CouldNotAssignAlunno;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Carbon\Carbon;
use Tests\TestCase;


class ClassiTest extends TestCase
{

    public function testAggiungiClasseInAnno()
    {
        $a = Anno::createAnno(2017);
        $t = ClasseTipo::all()->random();
        $this->assertNotNull($a->id);
        $this->assertCount(0, $a->classi()->get());
        $c = $a->aggiungiClasse($t);
        $this->assertCount(1, $a->classi()->get());
        $this->assertCount(0, $c->alunni()->get());
        $p1 = factory(Persona::class)->states("minorenne", "maschio")->create();
        $c->aggiungiAlunno($p1, Carbon::now());
        $this->assertCount(1, $c->alunni()->get());

    }

    public function testGetClassiInAnno()
    {
        $a = Anno::createAnno(2018);
        $t = ClasseTipo::all();

        // aggiuni due classi nell'anno e controlla
        $c1 = $a->aggiungiClasse($t->get(0));
        $p1 = factory(Persona::class)->states("minorenne", "maschio")->create();
        $c1->aggiungiAlunno($p1, Carbon::now());

        $c2 = $a->aggiungiClasse($t->get(3));
        $p2 = factory(Persona::class)->states("minorenne", "maschio")->create();
        $c2->aggiungiAlunno($p2, Carbon::now());

        $this->assertCount(2, $a->classi()->get());

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
}
