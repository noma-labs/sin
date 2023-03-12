<?php

namespace Database\Factories;

use App\Biblioteca\Models\Editore;
use Illuminate\Database\Eloquent\Factories\Factory;

class EditoreFactory extends Factory
{

    protected $model = Editore::class;

    public function definition()
    {
        return [
            'editore' => $this->faker->name,
            'tipedi' => 'S',
        ];
    }
}
