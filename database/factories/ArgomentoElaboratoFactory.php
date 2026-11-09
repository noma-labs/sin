<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Scuola\Models\ArgomentoElaborato;
use App\Scuola\Models\Elaborato;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ArgomentoElaborato>
 */
final class ArgomentoElaboratoFactory extends Factory
{
    protected $model = ArgomentoElaborato::class;

    public function definition(): array
    {
        return [
            'elaborato_id' => Elaborato::factory(),
            'argomento' => $this->faker->sentence(3),
            'materia' => $this->faker->randomElement(['Italiano', 'Storia', 'Geografia', 'Scienze', 'Arte', null]),
        ];
    }
}
