<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use Illuminate\Database\Eloquent\Factories\Factory;

final class FamigliaFactory extends Factory
{
    protected $model = Famiglia::class;

    public function definition()
    {
        return [
            'nome_famiglia' => $this->faker->name,
            'data_creazione' => $this->faker->date,
        ];
    }

    public function mammaDiVocazione(): self
    {
        return $this->state(fn (array $attributes): array => ['tipo' => TipoFamiglia::MammaDiVocazione]);
    }

    public function mammaNubile(): self
    {
        return $this->state(fn (array $attributes): array => ['tipo' => TipoFamiglia::MammaNubile]);
    }
}
