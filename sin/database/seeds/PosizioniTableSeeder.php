<?php

use Illuminate\Database\Seeder;
use App\Nomadelfia\Models\Posizione;


class PosizioniTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       if (App::environment() === 'production') exit();

        $data =[ 
            [
            'abbreviato' => "DADE",
            'nome' => 'Da Definire',
            'ordinamento' => 5,
        ], 
        [
            'abbreviato' => "EFFE",
            'nome' => 'Effettivo',
            'ordinamento' => 1,
        ],
        [
            'abbreviato' => "FIGL",
            'nome' => 'Figlio',
            'ordinamento' => 4,
        ],[
            'abbreviato' => "OSPP",
            'nome' => 'Ospite',
            'ordinamento' => 3,
        ],
        [
            'abbreviato' => "POST",
            'nome' => 'Postulante',
            'ordinamento' => 2,
        ]];
        DB::connection('db_nomadelfia')->table('posizioni')->insert($data);
    }
}
