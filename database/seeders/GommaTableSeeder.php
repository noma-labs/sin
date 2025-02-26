<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class GommaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 1,
            'nome' => '420/85 R30'
        ]);

        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 2,
            'nome' => '320/85 R20'
        ]);

        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 3,
            'nome' => '380/85 R30'
        ]);

        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 4,
            'nome' => '11,2 R24'
        ]);

        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 5,
            'nome' => '380/85 R28'
        ]);

        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 6,
            'nome' => '420/70 R34'
        ]);

        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 7,
            'nome' => '380/70 R24'
        ]);

        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 8,
            'nome' => '600/65 R38'
        ]);

        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 9,
            'nome' => '480/65 R28'
        ]);

        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 10,
            'nome' => '360/70 R24'
        ]);

        DB::connection('db_agraria')->table('gomma')->insert([
            'id' => 11,
            'nome' => '280/70 R16'
        ]);
    }
}
