<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public $connection = 'db_auth';

    public function up(): void
    {
        Permission::create(['name' => 'photo.*']);
        Permission::create(['name' => 'photo.view']);
        Permission::create(['name' => 'photo.update']);
        Permission::create(['name' => 'photo.store']);
        Permission::create(['name' => 'photo.download']);

        $photoAmmRole = Role::create(['name' => 'photo-amm']);
        $photoOpeRole = Role::create(['name' => 'photo-ope']);

        $photoAmmRole->givePermissionTo('photo.*');
        $photoOpeRole->givePermissionTo('photo.view');
    }

    public function down(): void
    {
    }
};
