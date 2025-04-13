<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Illuminate\Database\Eloquent\Factories\Factory;

final class GruppoFamiliareFactory extends Factory
{
    protected $model = GruppoFamiliare::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->name,
        ];
    }
}
