<?php

namespace Database\Seeders;

use App\Officina\Models\Impiego;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use Illuminate\Database\Seeder;


class VeicoloTableSeeder extends Seeder
{
    public function run()
    {
        Veicolo::factory(5)->create();

        Prenotazioni::factory(5)
            ->rientraInGiornata()
            ->create();
    }
}
