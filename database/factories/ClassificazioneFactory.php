<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Biblioteca\Models\Classificazione;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ClassificazioneFactory extends Factory
{
    protected $model = Classificazione::class;

    public function definition()
    {
        return [
            'descrizione' => $this->faker->name,
        ];
    }
}
