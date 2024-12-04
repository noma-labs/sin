<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Biblioteca\Models\Editore;
use Illuminate\Database\Eloquent\Factories\Factory;

final class EditoreFactory extends Factory
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
