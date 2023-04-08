<?php

namespace Database\Factories;

use App\Biblioteca\Models\Classificazione;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassificazioneFactory extends Factory
{
    protected $model = Classificazione::class;

    public function definition()
    {
        return [
            'descrizione' => $this->faker->name,
        ];
    }
}
