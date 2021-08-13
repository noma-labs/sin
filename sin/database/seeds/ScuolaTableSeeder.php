<?php

use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Illuminate\Database\Seeder;


class ScuolaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $a = Anno::createAnno(2021);
        $t = ClasseTipo::all();
        foreach($t as $tipo){
            $a->aggiungiClasse($tipo);
        }
    }
}
