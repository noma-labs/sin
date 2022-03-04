<?php

namespace Database\Factories;

use App\Nomadelfia\Models\GruppoFamiliare;
use Illuminate\Database\Eloquent\Factories\Factory;


class GruppoFamiliareFactory extends Factory
{

    protected $model = GruppoFamiliare::class;

    public function definition(){
        return [
            'nome'=> $this->faker->name,
        ];
    }
}
