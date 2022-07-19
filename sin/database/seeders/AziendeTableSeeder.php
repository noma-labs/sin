<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


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
                'nome_azienda' => 'Senza Azienda',
                'tipo' => 'azienda'
            ],
            [
                'nome_azienda' => 'Officina Meccanica',
                'tipo' => 'azienda'

            ],
            [
                'nome_azienda' => 'Falegnameria',
                'tipo' => 'azienda'

            ],
            [
                'nome_azienda' => 'Agraria',
                'tipo' => 'azienda'

            ],
            [
                'nome_azienda' => 'Elettrotecnica',
                'tipo' => 'azienda'

            ],
            [
                'nome_azienda' => 'Idraulica',
                'tipo' => 'azienda'

            ],
            [
                'nome_azienda' => 'scuola',
                'tipo' => 'azienda'

            ],

        ];
        DB::connection('db_nomadelfia')->table('aziende')->insert($data);
    }
}
