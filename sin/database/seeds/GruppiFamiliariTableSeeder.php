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
            'nome' => 'Bruciata',
        ], 
        [
            'nome' => 'Subiaco',
        ],
    ];
    DB::table('gruppi_familiari')->insert($data);
    }
}
