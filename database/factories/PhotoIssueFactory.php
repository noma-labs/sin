<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Photo\Models\Photo;
use App\Photo\Models\PhotoIssue;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PhotoIssueFactory extends Factory
{
    protected $model = PhotoIssue::class;

    public function definition(): array
    {
        return [
            'photo_id' => Photo::factory(),
            'persona_id' => null,
            'issue_type' => $this->faker->randomElement([
                'not_yet_born',
                'already_deceased',
                'year_mismatch_description',
                'year_like_number_in_description',
            ]),
            'photo_persona_name' => $this->faker->name(),
            'resolved_at' => null,
            'note' => null,
        ];
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'resolved_at' => now(),
        ]);
    }
}
