<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Photo\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        return [
            'sha' => $this->faker->sha1(),
            'source_file' => $this->faker->filePath(),
            'subjects' => collect($this->faker->words(5))->join(', '),
            'file_size' => $this->faker->numberBetween(200, 4000),
            'file_name' => $this->faker->word(),
            'mime_type' => $this->faker->fileExtension(),
            'image_height' => $this->faker->biasedNumberBetween(10, 3000),
            'image_width' => $this->faker->biasedNumberBetween(0.6000),
            'taken_at' => Carbon::now(),
        ];
    }

    public function inFolder(string $name)
    {
        return $this->state(function (array $attributes) use ($name) {
            // build a folder title like: 2022-05-23 XXXMQ Argomento foto
            return [
                'directory' => $name,
            ];
        });
    }

    public function takenAt(Carbon $date)
    {
        return $this->state(function (array $attributes) use ($date) {
            return [
                'taken_at' => $date->toDateString(),
            ];
        });
    }
}
