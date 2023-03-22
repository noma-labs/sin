<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        if (app()->environment('local')) {
            $this->call(LocalEnvironmentSeeder::class);
        }
    }
}
