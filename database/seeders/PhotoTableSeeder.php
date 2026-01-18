<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Nomadelfia\Persona\Models\Persona;
use App\Photo\Models\Photo;
use Illuminate\Database\Seeder;

final class PhotoTableSeeder extends Seeder
{
    public function run()
    {
        Photo::factory(10)
            ->has(Persona::factory()->count(3), 'persone')
            ->create();
    }
}
