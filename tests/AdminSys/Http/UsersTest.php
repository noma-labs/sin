<?php

declare(strict_types=1);

namespace Tests\Http\AdminSys;

use App\Admin\Controllers\LogsActivityController;
use App\Admin\Controllers\PermissionController;
use App\Admin\Controllers\RoleController;
use App\Admin\Controllers\UserController;
use App\Admin\Models\User;
use Spatie\Permission\Models\Role;

it('forbids not super-admin user to see admin dashboards', function (): void {
    $notSuperAdmin = User::create(['username' => 'not-super-admin', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
    $meccanicaAmm = Role::findByName('meccanica-amm');
    $notSuperAdmin->assignRole($meccanicaAmm);

    login($notSuperAdmin);

    $this->get(action([UserController::class, 'index']))
        ->assertForbidden();

    $this->get(action([PermissionController::class, 'index']))
        ->assertForbidden();

    $this->get(action([RoleController::class, 'index']))
        ->assertForbidden();

    $this->get(action([LogsActivityController::class, 'index']))
        ->assertForbidden();
});
