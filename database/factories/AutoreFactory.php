<?php

namespace Database\Factories;

use App\Biblioteca\Models\Autore;
use Illuminate\Database\Eloquent\Factories\Factory;

class AutoreFactory extends Factory
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
