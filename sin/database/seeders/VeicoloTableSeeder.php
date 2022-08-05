<?php

namespace Database\Seeders;

use App\Officina\Models\Impiego;
use App\Officina\Models\Veicolo;
use Illuminate\Database\Seeder;


class VeicoloTableSeeder extends Seeder
{
    public function run()
    {
        Veicolo::factory(5)->create();
    }
}
