<?php

namespace Database\Seeders;

use App\Biblioteca\Models\Autore;
use App\Biblioteca\Models\Classificazione;
use App\Biblioteca\Models\Libro;
use App\Officina\Models\Impiego;
use App\Officina\Models\Veicolo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BibliotecaTableSeeder extends Seeder
{
    public function run()
    {
        DB::connection('db_biblioteca')->table('classificazione')->insert(['descrizione'=>"per tutti"]);

        $autore = Autore::factory()
            ->has(
                Libro::factory()
                    ->count(3)
                    ->state(function (array $attributes) {
                        return ['classificazione_id' => Classificazione::all()->random()];
                    }),
                'libri'
            )
            ->create();
//        Libro::factory(5)->create();
//        Autore::factory(5)->create();
    }
}
