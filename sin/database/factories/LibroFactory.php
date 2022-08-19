<?php

namespace Database\Factories;


use App\Biblioteca\Models\Libro;
use Illuminate\Database\Eloquent\Factories\Factory;

class LibroFactory extends Factory
{

    protected $model = Libro::class;

    public function definition()
    {
        return [
            'titolo' => $this->faker->text(10),
            'collocazione' => $this->faker->text(5)."".$this->faker->numberBetween(0,100),
            'classificazione_id' => 1
        ];
    }
}

