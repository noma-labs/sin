<?php

namespace Tests\Http\AdminSys;

use App\Admin\Controllers\UserController;
use App\Admin\Models\User;
use Spatie\Permission\Models\Role;

it('only_super_admin_can_access_users', function () {
    $notSuperAdmin = User::create(['username' => 'not-super-admin', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
    $meccanicaAmm = Role::findByName('meccanica-amm');
    $notSuperAdmin->assignRole($meccanicaAmm);

    login($notSuperAdmin);

    $this->get(action([UserController::class, 'index']))
        ->assertForbidden();
});
