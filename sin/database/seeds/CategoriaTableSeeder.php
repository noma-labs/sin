<?php

use Illuminate\Database\Seeder;
use App\Nomadelfia\Models\Categoria;


class CategoriaTableSeeder extends Seeder
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
                "id"=>1,
            'nome' => 'Persona Interna',
        ], 
        [ 'id' => 4,
            'nome' => 'Persona esterna',
        ],
    ];
    DB::table('categorie')->insert($data);
    }
}
