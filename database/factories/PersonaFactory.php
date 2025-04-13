<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Nomadelfia\Persona\Models\Persona;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PersonaFactory extends Factory
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
            'nominativo' => $name.' '.$surname,
            'sesso' => 'M',
            'nome' => $name,
            'cognome' => $surname,
            'provincia_nascita' => 'GR',
            'data_nascita' => $this->faker->date,
            'id_arch_pietro' => 0,
        ];
    }

    public function cognome(string $cognome)
    {
        return $this->state(function (array $attributes) use ($cognome) {
            return [
                'cognome' => $cognome,
            ];
        });
    }

    public function femmina()
    {
        return $this->state(function (array $attributes) {
            return [
                'sesso' => 'F',
            ];
        });
    }

    public function maschio()
    {
        return $this->state(function (array $attributes) {
            return [
                'sesso' => 'M',
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

    public function maggiorenne(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'data_nascita' => Carbon::now()->subYears(30)->toDateString(),
            ];
        });
    }

    public function cinquantenne()
    {
        return $this->state(function () {
            return [
                'data_nascita' => Carbon::now()->subYears(50)->toDateString(),
            ];
        });
    }

    public function numeroElenco(string $num)
    {
        return $this->state(function (array $attributes) use ($num) {
            return [
                'numero_elenco' => $num,
            ];
        });
    }

    public function luogoNascita(string $luogo)
    {
        return $this->state(function (array $attributes) use ($luogo) {
            return [
                'provincia_nascita' => $luogo,
            ];
        });
    }

    public function withIdEnrico(int $id)
    {
        return $this->state(function (array $attributes) use ($id) {
            return [
                'id_alfa_enrico' => $id,
            ];
        });
    }
}
