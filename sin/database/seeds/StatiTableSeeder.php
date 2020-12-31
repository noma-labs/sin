<?php

use Illuminate\Database\Seeder;
use App\Nomadelfia\Models\Stato;

class StatiTableSeeder extends Seeder
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
                'stato' => "CDE",
                'nome' => 'Celi e di elezione',
            ], 
            [
                'stato' => "CEL",
                'nome' => 'Celibe',
            ], 
        ];

        DB::connection('db_nomadelfia')->table('stati')->insert($data);
    }
}
