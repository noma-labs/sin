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

    /**
     * A famiglia born from a marriage: its capofamiglia and moglie are sposati.
     */
    public function coniugi(): self
    {
        return $this->state(fn (array $attributes): array => [
            'tipo' => TipoFamiglia::Coniugi,
        ]);
    }
}
