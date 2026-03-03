<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Nomadelfia\Persona\Models\Persona;
use App\Patente\Models\Patente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patente>
 */
final class PatenteFactory extends Factory
{
    protected $model = Patente::class;

    public function definition(): array
    {
        return [
            'numero_patente' => $this->faker->unique()->bothify('??####'),
            'persona_id' => fn () => Persona::factory(),
            'data_rilascio_patente' => $this->faker->date(),
            'rilasciata_dal' => $this->faker->company(),
            'data_scadenza_patente' => $this->faker->date(),
            'stato' => $this->faker->randomElement([null, 'commissione']),
            'note' => $this->faker->optional()->sentence(),
        ];
    }

    public function persona(Persona $persona)
    {
        return $this->state(function (array $attributes) use ($persona) {
            return [
                'persona_id' => $persona->id,
            ];
        });
    }
}
