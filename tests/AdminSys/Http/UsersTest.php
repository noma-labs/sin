<?php

namespace Tests\Http\AdminSys;

use App\Admin\Controllers\LogsActivityController;
use App\Admin\Controllers\RisorsaController;
use App\Admin\Controllers\RoleController;
use App\Admin\Controllers\UserController;
use App\Admin\Models\User;
use Spatie\Permission\Models\Role;

it('forbids not super-admin user to see admin dashboards', function () {
    $notSuperAdmin = User::create(['username' => 'not-super-admin', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
    $meccanicaAmm = Role::findByName('meccanica-amm');
    $notSuperAdmin->assignRole($meccanicaAmm);

    login($notSuperAdmin);

    $this->get(action([UserController::class, 'index']))
        ->assertForbidden();

    $this->get(action([RisorsaController::class, 'index']))
        ->assertForbidden();

    $this->get(action([RoleController::class, 'index']))
        ->assertForbidden();

    $this->get(action([LogsActivityController::class, 'index']))
        ->assertForbidden();
});
