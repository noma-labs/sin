<?php

use Illuminate\Database\Seeder;
use App\Nomadelfia\Models\Stato;

class BibliotecaTableSeeder extends Seeder
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
                'autore' => "Lorem",
                'tipaut' => 'S',
            ]
        ];
        DB::connection('db_biblioteca')->table('autore')->insert($data);

        $data =[
            [
                'editore' => "Epsum",
                'tipedi' => 'S',
            ]
        ];
        DB::connection('db_biblioteca')->table('editore')->insert($data);
        $data =[
            [
                'descrizione' => "Per tutti",
            ]
        ];
        DB::connection('db_biblioteca')->table('classificazione')->insert($data);
    }
}
