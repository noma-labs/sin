<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Admin\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

final class DatabaseSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment('local')) {
            $this->createDefaultAdminUser();
            $this->call(NomadelfiaTableSeeder::class);
            $this->call(OfficinaMeccanicaTableSeeder::class);
            $this->call(BibliotecaTableSeeder::class);
            $this->call(ScuolaTableSeeder::class);
            $this->call(PhotoTableSeeder::class);
            $this->call(MezzoAgricoloTableSeeder::class);
        }
    }

     protected function createDefaultAdminUser(): void
    {
        $userAdmin = User::create(['username' => 'admin', 'email' => 'admin@email.it', 'password' => 'admin', 'persona_id' => 0]);
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $userAdmin->assignRole($superAdmin);
    }
}
