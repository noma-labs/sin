<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Agraria\Models\MezzoAgricolo;
use Illuminate\Database\Eloquent\Factories\Factory;

final class MezzoAgricoloFactory extends Factory
{
    protected $model = MezzoAgricolo::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->unique()->word(),
            'tot_ore' => $this->faker->numberBetween(0, 10000),
            'numero_telaio' => mb_strtoupper($this->faker->bothify('??######')),
            'filtro_olio' => $this->faker->word(),
            'filtro_gasolio' => $this->faker->word(),
            'filtro_servizi' => $this->faker->word(),
            'filtro_aria_int' => $this->faker->word(),
            'filtro_aria_ext' => $this->faker->word(),
            'gomme_ant' => null,
            'gomme_post' => null,
        ];
    }
}
