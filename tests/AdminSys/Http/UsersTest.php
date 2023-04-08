<?php

namespace Tests\Http\AdminSys;


it("only_super_admin_can_access_users", function(){
//        $notSuperAdmin = User::create(['username' => 'not-super-admin', 'email' => 'archivio@nomadelfia.it', 'password' => 'nomadelfia', 'persona_id' => 0]);
//        $meccanicaAmm = Role::findByName("meccanica-amm");
//        $notSuperAdmin->assignRole($meccanicaAmm);
//
//        $this->login($notSuperAdmin);
//
//        $this->get(action([UserController::class, 'index']))
//            ->assertSuccessful();
});