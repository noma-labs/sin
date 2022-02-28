<?php
namespace Database\Factories;


use App\Nomadelfia\Models\Persona;
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
            'nominativo' => $name . " " . Str::substr($surname, 0, 1),
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

