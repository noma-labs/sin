<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Biblioteca\Models\Autore;
use Illuminate\Database\Eloquent\Factories\Factory;

final class AutoreFactory extends Factory
{
    protected $model = Autore::class;

    public function definition()
    {
        return [
            'autore' => $this->faker->name,
            'tipaut' => 'S',
        ];
    }
}
