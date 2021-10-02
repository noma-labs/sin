<?php

namespace Tests;

use App\Admin\Models\Risorsa;
use App\Admin\Models\Ruolo;
use App\Admin\Models\Sistema;
use App\Admin\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function login(User $user = null): User
    {
        $nomadelfia = Sistema::create([
            'nome' => 'Popolazione Nomadelfia',
            'descrizione' => "Gestione della popolazione di Nomadelfia"
        ]);
        $roleAdmin = Ruolo::create([
            'nome' => 'Admin',
            'descrizione' => 'Amministratore del sistema (utenti, ruoli, permessi, backup,logs).'
        ]);
        $persona = Risorsa::create(['nome' => 'persona', 'sistema_id' => $nomadelfia->id]);
        $roleAdmin->risorse()->save($persona, [
            "visualizza" => "1",
            "inserisci" => "1",
            "elimina" => "1",
            "modifica" => "1",
            "prenota" => "1",
            "esporta" => "1",
            "svuota" => "1",
        ]);
        $user = (!is_null($user) ? $user : User::create([
            'username' => 'Admin',
            'email' => 'archivio@nomadelfia.it',
            'password' => 'nomadelfia',
            'persona_id' => 1
        ]));
        $user->assignRole($roleAdmin);

        $this->actingAs($user);

        return $user;
    }
}
