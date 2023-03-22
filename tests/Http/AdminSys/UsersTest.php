<?php

namespace Tests\Http\AdminSys;

use App\Admin\Controllers\UserController;
use App\Admin\Models\User;
use App\Scuola\Controllers\ScuolaController;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsersTest extends TestCase
{

    /** @test */
    public function only_super_admin_can_access_users()
    {
//        $notSuperAdmin = User::create(['username' => 'not-super-admin', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
//        $meccanicaAmm = Role::findByName("meccanica-amm");
//        $notSuperAdmin->assignRole($meccanicaAmm);
//
//        $this->login($notSuperAdmin);
//
//        $this->get(action([UserController::class, 'index']))
//            ->assertSuccessful();

    }

}