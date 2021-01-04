<?php

use Illuminate\Database\Seeder;
use App\Nomadelfia\Models\GruppoFamiliare;


class GruppiFamiliariTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data =[ 
            [
            'nome' => 'Cenacolo',
        ], 
        [
            'nome' => 'Diaccialone',
        ],
        [
            'nome' => 'Nazareth',
        ],
        [
            'nome' => 'Assunta',
        ],
        [
            'nome' => 'Betlam Basso',
        ],
        [
            'nome' => 'Betlem Alto',
        ],
        [
            'nome' => 'Bruciata',
        ],
        [
            'nome' => 'Sughera',
        ],
        [
            'nome' => 'Rosellana',
        ],
        [
            'nome' => 'Subiaco',
        ],
        [
            'nome' => 'Poggetto',
        ],
        [
            'nome' => 'Giovanni Paolo II',
        ],
    ];
    DB::connection('db_nomadelfia')->table('gruppi_familiari')->insert($data);
    }
}
