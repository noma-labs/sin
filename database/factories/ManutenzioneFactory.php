<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Agraria\Models\Manutenzione;
use App\Agraria\Models\MezzoAgricolo;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ManutenzioneFactory extends Factory
{
    protected $model = Manutenzione::class;

    public function definition()
    {
        return [
            'mezzo_agricolo' => MezzoAgricolo::factory(),
            'data' => $this->faker->date(),
            'ore' => $this->faker->numberBetween(0, 10000),
            'spesa' => $this->faker->randomFloat(2, 0, 1000),
            'persona' => $this->faker->name(),
            'lavori_extra' => $this->faker->sentence(3),
        ];
    }
}
