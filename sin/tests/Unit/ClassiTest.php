<?php

namespace Tests\Unit;

use App\Scuola\Exceptions\CouldNotAssignAlunno;

use App\Nomadelfia\Models\Classe;
use App\Nomadelfia\Models\ClasseTipo;
use App\Nomadelfia\Models\Persona;
use Tests\TestCase;
use Carbon\Carbon;


class ClassiTest extends TestCase
{

    public function testCreaClasseInAnnoScolastico()
    {
        $t = ClasseTipo::all();
        $this->assertGreaterThan(0, $t->count());

        // aggiuni una classe in un anno scolastico
        $classe = Classe::aggiungiClasse("2019/2020", $t->random());
        $data_inizio = Carbon::now();
        $persona = factory(Persona::class)->states("minorenne", "maschio")->create();
        $this->assertCount(0, $classe->alunni()->get());
        $classe->aggiungiAlunno($persona, $data_inizio);
        $this->assertCount(1, $classe->alunni()->get());
    }

    public function testCreaClasseError()
    {
        // aggiuni una classe in un anno scolastico
        $this->expectException(CouldNotAssignAlunno::class);
        $classe = Classe::aggiungiClasse("20192020", ClasseTipo::all()->random());
    }
}
