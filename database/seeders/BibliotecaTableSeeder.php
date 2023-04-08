<?php

namespace Database\Seeders;

use App\Biblioteca\Models\Autore;
use App\Biblioteca\Models\Classificazione;
use App\Biblioteca\Models\Libro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BibliotecaTableSeeder extends Seeder
{
    public function run()
    {
        DB::connection('db_biblioteca')->table('classificazione')->insert(['descrizione' => 'per tutti']);
        DB::connection('db_biblioteca')->table('libro')->insert(['collocazione' => 'AAA000']);
        DB::connection('db_biblioteca')->table('editore')->insert(['editore' => 'AAA editore']);

        Autore::factory()
            ->has(
                Libro::factory()
                    ->count(3)
                    ->state(function (array $attributes) {
                        return ['classificazione_id' => Classificazione::all()->random()];
                    }),
                'libri'
            )
            ->create();
    }
}
