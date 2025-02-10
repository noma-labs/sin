<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment('local')) {
            $this->call(LocalEnvironmentSeeder::class);
        }
    }
}
