<?php

namespace Database\Factories;


use Domain\Nomadelfia\Persona\Models\Persona;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PersonaFactory extends Factory
{

    protected $model = Persona::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->firstName;
        $surname = $this->faker->lastName;
        return [
            'nominativo' => $name . " " . $surname,
            'sesso' => "M",
            'nome' => $name,
            "cognome" => $surname,
            "provincia_nascita" => "GR",
            'data_nascita' => $this->faker->date,
            'id_arch_pietro' => 0,
            'id_arch_enrico' => 0
        ];
    }

    public function femmina()
    {
        return $this->state(function (array $attributes) {
            return [
                'sesso' => "F",
            ];
        });
    }

    public function maschio()
    {
        return $this->state(function (array $attributes) {
            return [
                'sesso' => "M",
            ];
        });
    }

    public function diEta(int $year)
    {
        return $this->state(function (array $attributes) use ($year) {
            return [
                'data_nascita' => Carbon::now()->subYears($year)->toDateString(),
            ];
        });
    }

    public function nato(Carbon $data_nascita)
    {
        return $this->state(function (array $attributes) use ($data_nascita) {
            return [
                'data_nascita' => $data_nascita->toDateString(),
            ];
        });
    }

    public function minorenne()
    {
        return $this->state(function (array $attributes) {
            return [
                'data_nascita' => Carbon::now()->subYears(10)->toDateString(),
            ];
        });
    }

    public function maggiorenne()
    {
        return $this->state(function (array $attributes) {
            return [
                'data_nascita' => Carbon::now()->subYears(30)->toDateString(),
            ];
        });
    }

    public function cinquantenne()
    {
        return $this->state(function (array $attributes) {
            return [
                'data_nascita' => Carbon::now()->subYears(50)->toDateString(),
            ];
        });
    }
}
