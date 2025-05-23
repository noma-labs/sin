<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Nomadelfia\Azienda\Models\Azienda;
use Illuminate\Database\Eloquent\Factories\Factory;

final class AziendaFactory extends Factory
{
    protected $model = Azienda::class;

    public function definition()
    {
        return [
            'nome_azienda' => $this->faker->firstName,
            'descrizione_azienda' => 'a description',
            'tipo' => 'azienda',
        ];
    }

    public function incarico()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo' => 'incarico',
            ];
        });
    }
}
