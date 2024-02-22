<?php

namespace Database\Seeders;

use Domain\Photo\Actions\ExtractSubjectsFromPhotoAction;
use Domain\Photo\Models\Photo;
use Illuminate\Database\Seeder;

class PhotoTableSeeder extends Seeder
{
    public function run(): void
    {
        Photo::factory(10)->create();

        $act = (new ExtractSubjectsFromPhotoAction());
        $act->execute();
    }
}
