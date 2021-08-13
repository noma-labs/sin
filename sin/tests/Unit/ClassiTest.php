<?php

namespace Tests\Unit;

use App\Scuola\Exceptions\CouldNotAssignAlunno;

use App\Scuola\Models\Classe;
use App\Scuola\Models\ClasseTipo;
use App\Nomadelfia\Models\Persona;
use App\Scuola\Models\Anno;
use Tests\TestCase;
use Carbon\Carbon;


class ClassiTest extends TestCase
{

//    public function testGetClassiInAnno()
//    {
////        $t = Anno::();
//    }
    public function testAggiungiClasseInAnno()
    {
        $a = Anno::create([
            'id' => "2017",
            'scolastico' => "2017/2018"
        ]);
        $t = ClasseTipo::all()->random();
        $this->assertNotNull($a->id);
        $a->aggiungiClasse($t);
        $this->assertCount(1, $a->classi()->get());
    }

//    public function testGetClassiInAnno()
//    {
//        $a = Anno::create([
//            'id' => "2017",
//            'scolastico' => "2017/2018"
//        ]);
//        $t = ClasseTipo::all();

//        // aggiuni due classi nell'anno e controlla
//        $c1 = Classe::aggiungiClasse($a, $t->get(0));
//        $p1 = factory(Persona::class)->states("minorenne", "maschio")->create();
//        $c1->aggiungiAlunno($p1, Carbon::now());
//
//        $c2 = Classe::aggiungiClasse($a, $t->get(3));
//        $p2 = factory(Persona::class)->states("minorenne", "maschio")->create();
//        $c2->aggiungiAlunno($p2, Carbon::now());
//
//        $c3 = Classe::aggiungiClasse($a, $t->get(2));
//        $p3 = factory(Persona::class)->states("minorenne", "femmina")->create();
//        $c3->aggiungiAlunno($p3, Carbon::now());
//
//        $this->assertCount(2, Classe::perAnno("2021/2022"));
//    }

//    public function testCreaClasseInAnnoScolastico()
//    {
//        $a = Anno::create([
//            'id' => "2018",
//            'scolastico' => "2017/2018"
//        ]);
//
//        $t = ClasseTipo::all();
//        $this->assertGreaterThan(0, $t->count());
//
//        // aggiuni una classe in un anno scolastico
//        $tipo = $t->random();
//        $classe = Classe::aggiungiClasse($a, $tipo);
//        $persona = factory(Persona::class)->states("minorenne", "maschio")->create();
//        $this->assertNotEmpty($classe->id);
//
//        $this->assertCount(0, $classe->alunni()->get());
//        $classe->aggiungiAlunno($persona, Carbon::now());
//        $this->assertCount(1, $classe->alunni()->get());
//        $this->assertEquals($tipo->id, $classe->tipo->id);
//    }

//    public function testCreaClasseError()
//    {
//        $a = Anno::create([
//            'id' => "2017",
//            'scolastico' => "2017/2018"
//        ]);
//        // aggiuni una classe in un anno scolastico
//        $this->expectException(CouldNotAssignAlunno::class);
//        Classe::aggiungiClasse($a, ClasseTipo::all()->random());
//    }
}
