<?php

namespace Database\Seeders;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Photo\Models\Photo;
use Illuminate\Database\Seeder;

class PhotoTableSeeder extends Seeder
{
    public function run()
    {
        Photo::factory()
            ->has(Persona::factory()->count(3), 'persone')
            ->create();

    }
}
