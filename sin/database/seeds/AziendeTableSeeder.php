<?php

use Illuminate\Database\Seeder;


class AziendeTableSeeder extends Seeder
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
                'nome_azienda' => 'RTN',
                "data_azienda" => "2021-04-01"
            ],
            [
                'nome_azienda' => 'Meccanica',
                "data_azienda" => "2021-04-01"
            ],
            [
                'nome_azienda' => 'Agraria',
                "data_azienda" => "2021-04-01"
            ],

        ];
        DB::connection('db_nomadelfia')->table('aziende')->insert($data);
    }
}
