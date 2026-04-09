<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Photo\Models\DbfAll;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DbfAllFactory extends Factory
{
    protected $model = DbfAll::class;

    public function definition(): array
    {
        $datnum = mb_str_pad((string) $this->faker->numberBetween(1000, 99999), 5, '0', STR_PAD_LEFT);
        $anum = mb_str_pad((string) ($this->faker->numberBetween((int) $datnum, (int) $datnum + 10)), 5, '0', STR_PAD_LEFT);

        return [
            'source' => $this->faker->randomElement(['foto', 'slide', 'dia120']),
            'datnum' => $datnum,
            'anum' => $anum,
            'cddvd' => '',
            'hdint' => '',
            'hdext' => '',
            'sc' => '',
            'fi' => 'analog',
            'tp' => '',
            'nfo' => $this->faker->numberBetween(1, 50),
            'data' => $this->faker->dateTime(),
            'localita' => $this->faker->city(),
            'argomento' => $this->faker->word(),
            'descrizione' => $this->faker->text(100),
        ];
    }
}
