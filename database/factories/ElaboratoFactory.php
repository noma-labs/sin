<?php

namespace Database\Factories;

use App\Scuola\Models\Elaborato;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElaboratoFactory extends Factory
{
    protected $model = Elaborato::class;

    public function definition()
    {
        $year = $this->faker->numberBetween(1976, 2024);

        return [
            'collocazione' => $this->faker->regexify('[A-Z]{3}[0-9]{3}'),
            'anno_scolastico' => $year.'/'.$year + 1,
            'titolo' => $this->faker->sentence,
            'classi' => $this->faker->words(3, true),
            'file_path' => $this->faker->filePath(),
            'file_mime_type' => $this->faker->mimeType,
            'file_size' => $this->faker->numberBetween(1000, 1000000),
            'file_hash' => $this->faker->sha256,
            'dimensione' => $this->faker->regexify('[0-9]{2}x[0-9]{2}'),
            'rilegatura' => $this->faker->word,
            'note' => $this->faker->paragraph,
            'created_at' => now(),
            'updated_at' => now(),
            'libro_id' => null,
        ];
    }
}
