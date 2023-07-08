<?php

namespace Database\Factories;

use Domain\Photo\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        return [
            'Sha' => $this->faker->word(),
            'SourceFile' => $this->faker->word(),
            'FileName' => $this->faker->name(),
            'TakenAt' => Carbon::now(),
            'Subjects' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
