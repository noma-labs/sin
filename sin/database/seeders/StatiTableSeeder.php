<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

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
                'nome' => 'Celibe di elezione',
            ], 
            [
                'stato' => "CEL",
                'nome' => 'Celibe',
            ], 
            [
                'stato' => "MAM",
                'nome' => 'NOMADELFA MAMMA',
            ], 
            [
                'stato' => "MAV",
                'nome' => 'MAMMA DI VOCAZIONE',
            ], 
            [
                'stato' => "NUB",
                'nome' => 'Nubile',
            ], 
            [
                'stato' => "MAN",
                'nome' => 'MAMMA NUBILE',
            ], 
            [
                'stato' => "SAC",
                'nome' => 'SACERDOTE',
            ], 
            [
                'stato' => "SEP",
                'nome' => 'CONIUGE SEPARATO(A)',
            ], 
            [
                'stato' => "SPO",
                'nome' => 'SPOSATO/A',
            ],
            [
                'stato' => "VED",
                'nome' => 'VEDOVO/A',
            ],  
        ];
        DB::connection('db_nomadelfia')->table('stati')->insert($data);
    }
}
