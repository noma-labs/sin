<?php

namespace Tests\Officina\Unit;

use Carbon\Carbon;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use App\Officina\Models\Prenotazioni;
use Domain\Nomadelfia\Persona\Models\Persona;
use Spatie\PestPluginTestTime\TestTime;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    Prenotazioni::truncate();
});

it("show the active booking of today", function(){
    testTime()->freeze('2024-03-23 12:00:00');

    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-23 08:00'), Carbon::parse('2024-03-23 12:00'))->create();
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-23 15:00'), Carbon::parse('2024-03-23 17:00'))->create();
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 12:00'), Carbon::parse('2024-03-23 12:00'))->create();
    Prenotazioni::factory()->prenotata(Carbon::parse('2024-03-22 12:00'), Carbon::parse('2024-03-25 12:00'))->create(); // this is excluded because it ends tomorrow

    expect(Prenotazioni::today()->count())->toBe(3);

    expect(Prenotazioni::ActiveIn(Carbon::parse('2024-03-23 00:00'), Carbon::parse('2024-03-23 23:59'))->count())->toBe(3);
    expect(Prenotazioni::ActiveIn(Carbon::parse('2024-03-23 07:00'), Carbon::parse('2024-03-23 08:00'))->count())->toBe(1);
    expect(Prenotazioni::ActiveIn(Carbon::parse('2024-03-23 07:00'), Carbon::parse('2024-03-23 10:00'))->count())->toBe(2);
    expect(Prenotazioni::ActiveIn(Carbon::parse('2024-03-23 10:00'), Carbon::parse('2024-03-23 15:00'))->count())->toBe(2);
    expect(Prenotazioni::ActiveIn(Carbon::parse('2024-03-23 12:00'), Carbon::parse('2024-03-23 15:00'))->count())->toBe(0);
    expect(Prenotazioni::ActiveIn(Carbon::parse('2024-03-23 16:00'), Carbon::parse('2024-03-23 17:00'))->count())->toBe(1);
    expect(Prenotazioni::ActiveIn(Carbon::parse('2024-03-23 17:00'), Carbon::parse('2024-03-23 18:00'))->count())->toBe(0);
    expect(Prenotazioni::ActiveIn(Carbon::parse('2024-03-23 18:00'), Carbon::parse('2024-03-23 19:00'))->count())->toBe(0);

   });


//it("show_prenotazioni_attive_ieri", function(){
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
