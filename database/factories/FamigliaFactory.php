<?php

namespace Database\Factories;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Illuminate\Database\Eloquent\Factories\Factory;

class FamigliaFactory extends Factory
{

    protected $model = Famiglia::class;

    public function definition(){
        return [
            'nome_famiglia'=> $this->faker->name,
            'data_creazione'=> $this->faker->date,
        ];
    }
}
