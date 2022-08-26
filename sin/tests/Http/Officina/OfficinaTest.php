<?php

namespace Tests\Http\Officina;

use Domain\Nomadelfia\Persona\Models\Persona;
use App\Officina\Actions\CreatePrenotazioneAction;
use App\Officina\Controllers\PrenotazioniController;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Composer\CaBundle\CaBundle;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;
use Tests\MigrateFreshDB;
use Tests\TestCase;


class OfficinaTest extends TestCase
{

    /** @test */
    public function admin_can_create_prenotazione()
    {
        $v = Veicolo::factory()->create();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $p = Persona::find($persona->id);
        $m = Persona::factory()->maggiorenne()->maschio()->create();
        $u = Uso::all()->random();
        $this->assertNotEmpty($u);

        $this->login();
        $now = Carbon::now();
        $this->post(action([PrenotazioniController::class, 'prenotazioniSucc'], [
            'nome' => $p->id,
            'veicolo' => $v->id,
            'meccanico' => $m->id,
            'data_par' => $now->toDateString(),
            'ora_par' => '08:00',
            'data_arr' => $now->toDateString(),
            'ora_arr' => '11:00',
            'uso' => $u->ofus_iden,
            'destinazione' => 'my-destination'
        ]))->assertRedirect(route('officina.prenota'));

        $this->assertDatabaseHas('db_meccanica.prenotazioni', [
            'destinazione' => 'my-destination'
        ]);

        $this->get(action([PrenotazioniController::class, 'prenotazioni'], ['giorno' => 'oggi']))
            ->assertSuccessful();

    }

//    /** @test */
//    public function show_prenotazioni_attive_oggi()
//    {
//        $veicolo = Veicolo::factory()->create();
//        $veicolo2 = Veicolo::factory()->create();
//        $persona = Persona::factory()->maggiorenne()->maschio()->create();
//        $cliente = Persona::find($persona->id);
//        $meccanico = Persona::factory()->maggiorenne()->maschio()->create();
//        $uso = Uso::all()->random();
//
//        $oggi = Carbon::now()->toDateString();
//        $yesterday = Carbon::now()->subDay()->toDateString();
//        $tomorrow = Carbon::now()->addDay()->toDateString();
//
//        Prenotazioni::factory()
//            ->rientraInGiornata('08:00', '12:00')
//            ->create( [
//                'cliente_id' => $cliente->id,
//                'veicolo_id' => $veicolo->id,
//                'meccanico_id' => $meccanico->id,
//                'uso_id' => $uso->ofus_iden,
//            ]);
//        Prenotazioni::factory()
//            ->rientraInGiornata('15:00', '17:00')
//            ->create( [
//                'cliente_id' => $cliente->id,
//                'veicolo_id' => $veicolo2->id,
//                'meccanico_id' => $meccanico->id,
//                'uso_id' => $uso->ofus_iden,
//            ]);
//        Prenotazioni::factory()
//            ->partitaIeri('15:00')
//            ->ritornaOggi("12:00")
//            ->create( [
//                'cliente_id' => $cliente->id,
//                'veicolo_id' => $veicolo2->id,
//                'meccanico_id' => $meccanico->id,
//                'uso_id' => $uso->ofus_iden,
//            ]);
//        // attive oggi
//        $this->assertCount(3, Prenotazioni::ActiveToday()->get());
//        // attive in
//        $this->assertCount(2, Prenotazioni::ActiveIn($oggi, '00:00', $oggi, '23:59')->get());
//        $this->assertCount(1, Prenotazioni::ActiveIn($oggi, '07:00', $oggi, '10:00')->get());
//        $this->assertCount(1, Prenotazioni::ActiveIn($oggi, '10:00', $oggi, '15:00')->get());
//        $this->assertCount(0, Prenotazioni::ActiveIn($oggi, '07:00', $oggi, '08:00')->get());
//        $this->assertCount(0, Prenotazioni::ActiveIn($oggi, '12:00', $oggi, '15:00')->get());
//        $this->assertCount(1, Prenotazioni::ActiveIn($oggi, '15:00', $oggi, '16:00')->get());
//        $this->assertCount(1, Prenotazioni::ActiveIn($oggi, '16:00', $oggi, '17:00')->get());
//        $this->assertCount(2, Prenotazioni::ActiveIn($oggi, '10:00', $oggi, '16:00')->get());
//
//    }

    /** @test */
//    public function show_prenotazioni_attive_ieri()
//    {
//        Artisan::call('migrate:fresh', ['--database' => 'db_officina', '--path' => 'database/migrations/officina']);
//        $veicolo = Veicolo::factory()->create();
//        $veicolo2 = Veicolo::factory()->create();
//        $persona = Persona::factory()->maggiorenne()->maschio()->create();
//        $cliente = Persona::find($persona->id);
//        $meccanico = Persona::factory()->maggiorenne()->maschio()->create();
//        $uso = Uso::all()->random();
//
//        $oggi = Carbon::now()->toDateString();
//        $yesterday = Carbon::now()->subDay()->toDateString();
//        $tomorrow = Carbon::now()->addDay()->toDateString();
//
//        Prenotazioni::factory()
//            ->partitaIeri('15:00')
//            ->ritornaIeri('18:00')
//            ->create([
//                'cliente_id' => $cliente->id,
//                'veicolo_id' => $veicolo2->id,
//                'meccanico_id' => $meccanico->id,
//                'uso_id' => $uso->ofus_iden,
//            ]);
//        Prenotazioni::factory()
//            ->partita(Carbon::now()->subDays(2))
//            ->ritornaIeri('18:00')
//            ->create([
//                'cliente_id' => $cliente->id,
//                'veicolo_id' => $veicolo2->id,
//                'meccanico_id' => $meccanico->id,
//                'uso_id' => $uso->ofus_iden,
//            ]);
//        $this->assertCount(2, Prenotazioni::ActiveYesterday()->get());
//        $this->assertCount(0, Prenotazioni::ActiveToday()->get());
//
//        $this->assertCount(1, Prenotazioni::ActiveIn($yesterday, '00:00', $yesterday, '23:59')->get());
//
//    }
}
