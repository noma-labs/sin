<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Persona;
use App\Officina\Actions\CreatePrenotazioneAction;
use App\Officina\Controllers\PrenotazioniController;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Tests\CreatesApplication;
use Tests\MigrateFreshDB;
use Tests\TestCase;


class OfficinaTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    /** @test */
//    public function admin_can_create_prenotazione()
//    {
//
//
//        $v = Veicolo::factory()->create();
//        $persona = Persona::factory()->maggiorenne()->maschio()->create();
//        $p = Persona::find($persona->id);
//        $m = Persona::factory()->maggiorenne()->maschio()->create();
//        $u = Uso::all()->random();
//        $this->assertNotEmpty($u);
//
//        $this->login();
//        $now = Carbon::now();
//
//        $this->post(action([PrenotazioniController::class, 'prenotazioniSucc'], [
//            'nome' => $p->id,
//            'veicolo' => $v->id,
//            'meccanico' => $m->id,
//            'data_par' => $now->toDateString(),
//            'ora_par' => '08:00',
//            'data_arr' => $now->toDateString(),
//            'ora_arr' => '11:00',
//            'uso' => $u->ofus_iden,
//            'destinazione' => 'my-destination'
//        ]))->assertRedirect(route('officina.prenota'));
//
//        $this->assertDatabaseHas('db_meccanica.prenotazioni', [
//            'destinazione' => 'my-destination'
//        ]);
//
//        $this->get(action([PrenotazioniController::class, 'prenotazioni'], ['giorno' => 'oggi']))
//            ->assertSuccessful();
//
//    }

    /** @test */
    public function show_today_yesterday_tomorrow_prenotazioni()
    {
        $veicolo = Veicolo::factory()->create();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $cliente = Persona::find($persona->id);
        $meccanico = Persona::factory()->maggiorenne()->maschio()->create();
        $uso = Uso::all()->random();

        Prenotazioni::factory()
            ->partitaIeri()
            ->ritornaDomani()
            ->count(1)
            ->sequence(
                [
                    'cliente_id' => $cliente->id,
                    'veicolo_id' => $veicolo->id,
                    'meccanico_id' => $meccanico->id,
                    'uso_id' => $uso->ofus_iden,
                ],
            )->create();
        $now = Carbon::now()->toDateString();
        $p = Prenotazioni::AttiveIn($now , "07:00", $now , "14:00");
        $this->assertCount(1, $p->get());
    }
}
