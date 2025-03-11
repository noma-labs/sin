<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Veicolo;
use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Seeder;

final class OfficinaMeccanicaTableSeeder extends Seeder
{
    public function run()
    {
        $meccanica = Azienda::where('nome_azienda', 'like', 'Officina%')->first();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();

        $persona->aziende()->attach($meccanica, [
            'stato' => 'Attivo',
            'data_inizio_azienda' => Carbon::now()->toDateString(),
            'mansione' => Azienda::MANSIONE_RESPONSABILE,
        ]);
        $veicoli = Veicolo::factory(5)
            ->impiegoGrosseto()
            ->tipologiaMacchina()
            ->create();

        $personale = Veicolo::factory(2)
            ->impiegoPersonale()
            ->tipologiaMacchina()
            ->create();

        Prenotazioni::factory()
            ->prenotata(Carbon::now(), Carbon::now()->addHour(2))
            ->veicolo($veicoli->random())
            ->create();

        Prenotazioni::factory()
            ->prenotata(Carbon::now(), Carbon::now()->addHour(2))
            ->veicolo($personale->random())
            ->create();
    }
}
