<?php

namespace Database\Factories;

use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
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
