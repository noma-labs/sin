<?php

namespace Tests;

use App\Admin\Models\Risorsa;
use App\Admin\Models\Ruolo;
use App\Admin\Models\Sistema;
use App\Admin\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Http\Request;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function login(User $user = null): User
    {
        if (is_null($user)) {
            // TODO: don't create the use and tole but use the seeded ones
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
            $user = User::create([
                'username' => 'Admin',
                'email' => 'archivio@nomadelfia.it',
                'password' => 'nomadelfia',
                'persona_id' => 1
            ]);
            $user->assignRole($roleAdmin);
        }

        $this->actingAs($user);

        return $user;
    }

    public function createRequest($method, $uri): Request
    {
        $symfonyRequest = SymfonyRequest::create(
            $uri,
            $method
        );

        return Request::createFromBase($symfonyRequest);
    }
}
