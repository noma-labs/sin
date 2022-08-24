<?php

namespace Database\Factories;

use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Incarico;
use Illuminate\Database\Eloquent\Factories\Factory;


class IncaricoFactory extends Factory
{

    protected $model = Incarico::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->name,
            'descrizione' => "a description",
        ];
    }

    public function incarico()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo' => "incarico",
            ];
        });
    }
}


