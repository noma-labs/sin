<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Persona;
use App\Officina\Actions\CreatePrenotazioneAction;
use App\Officina\Controllers\PrenotazioniController;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use Tests\CreatesApplication;
use Tests\MigrateFreshDB;
use Tests\TestCase;


class OfficinaTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function testPrenotazioni()
    {
        $v = Veicolo::factory()->create();
        $p = Persona::factory()->maggiorenne()->maschio()->create();
        $p = Persona::find($p->id);
        $m = Persona::factory()->maggiorenne()->maschio()->create();
        $u = Uso::all()->random();
        $this->assertNotEmpty($u);
        $act = new CreatePrenotazioneAction();

//        $act->execute($p,
//            $v,
//            $m,
//            "2022-01-01",
//            '2022-01-01',
//            "08:00",
//            "11:00",
//            $u,
//            "note",
//            "grosseto"
//        );

        $this->login();
        $this->withoutExceptionHandling();
        $this->post(action([PrenotazioniController::class, 'prenotazioniSucc'],[
            'nome' => $p->id,
            'veicolo' => $v->id,
            'meccanico' => $m->id,
            'data_par' => '2022-01-01',
            'ora_par' => '08:00',
            'data_arr' => "2022-01-01",
            'ora_arr' => '11:00',
            'uso' => $u->ofus_iden,
            'destinazione' => 'rere'
        ]))->assertStatus(200);





    }

}
