<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CaricheTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'nome' => 'Presidente',
                "org" => "associazione",
                'num' => 1,
                'ord' => 1
            ],
            [
                'nome' => 'Vicepresidente',  // 2 vicepresidenti
                "org" => "associazione",
                'num' => 2,
                'ord' => 2
            ],
            [
                'nome' => 'Economo',
                "org" => "associazione",
                'num' => 1,
                'ord' => 4
            ],
            [
                'nome' => 'Vice economo',
                "org" => "associazione",
                'num' => 2,
                'ord' => 5
            ],
            [
                'nome' => 'Capogruppo',
                "org" => "associazione",
                'num' => 12,
                'ord' => 6
            ],
            [
                'nome' => 'Capogiudice',
                "org" => "associazione",
                'num' => 1,
                'ord' => 7
            ],
            [
                'nome' => 'Giudici',
                "org" => "associazione",
                'num' => 2,
                'ord' => 8
            ],
            [
                'nome' => 'Consiglio degli anziani',  // 12 persone e 1 è coordinatore
                "org" => "associazione",
                'num' => 12,
                'ord' => 10
            ],
            [
                'nome' => 'Consiglio degli anziani - coordinatore',  // 12 persone e 1 è coordinatore
                "org" => "associazione",
                'num' => 1,
                'ord' => 11
            ],
            [
                'nome' => 'Congresso dei figli - presidente',
                "org" => "associazione",
                'num' => 1,
                'ord' => 12
            ],
            [
                'nome' => 'Congresso dei figli - vicepresidente',
                "org" => "associazione",
                'num' => 1,
                'ord' => 13
            ],
            [
                'nome' => 'Congresso dei figli - consigliere',  // 4 consiglieri
                "org" => "associazione",
                'num' => 4,
                'ord' => 14
            ],
            // Solidarietà nomadelfia ODV
            [
                'nome' => 'Presidente',
                "org" => "solidarieta",
                'num' => 1,
                'ord' => 1
            ],
            [
                'nome' => 'Vicepresidente',
                "org" => "solidarieta",
                'num' => 1,
                'ord' => 2
            ],
            [
                'nome' => 'Tesoriere',
                "org" => "solidarieta",
                'num' => 1,
                'ord' => 3
            ],
            [
                'nome' => 'Consiglio direttivo', // 2 persone
                "org" => "solidarieta",
                'num' => 2,
                'ord' => 4
            ],
            // Fondazione Nomadelfia
            [
                'nome' => 'Presidente',
                "org" => "fondazione",
                'num' => 1,
                'ord' => 1

            ],
            [
                'nome' => 'Segretario',
                "org" => "fondazione",
                'num' => 1,
                'ord' => 2
            ],
            [
                'nome' => 'Revisori Conti',
                "org" => "fondazione",
                'num' => 1,
                'ord' => 3
            ],
            // Cooperativa Agricola
            [
                'nome' => 'Presidente',
                "org" => "agricola",
                'num' => 1,
                'ord' => 1
            ],
            [
                'nome' => 'Consigliere',  //  2 consiglieri
                "org" => "agricola",
                'num' => 2,
                'ord' => 2
            ],
            [
                'nome' => 'Responsabile Tecnico', // 6 persone
                "org" => "agricola",
                'num' => 6,
                'ord' => 3
            ],
            // Cooperativa Culturale
            [
                'nome' => 'Presidente',
                "org" => "culturale",
                'num' => 1,
                'ord' => 1
            ],
            [
                'nome' => 'Consiglieri',  // 2 consiglieri
                "org" => "culturale",
                'num' => 2,
                'ord' => 2
            ],

        ];
        DB::connection('db_nomadelfia')->table('cariche')->insert($data);
    }
}
