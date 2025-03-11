<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Photo\Models\Photo;
use Illuminate\Database\Seeder;

final class PhotoTableSeeder extends Seeder
{
    public function run()
    {
        Photo::factory()
            ->has(Persona::factory()->count(3), 'persone')
            ->create();
    }
}
