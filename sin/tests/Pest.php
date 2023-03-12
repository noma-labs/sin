<?php

use App\Admin\Models\User;
use Illuminate\Support\Facades\Artisan;

use Tests\TestCasePest;
use function Pest\Laravel\actingAs;


uses(TestCasePest::class)->in('Biblioteca', 'Scuola', 'Popolazione');


uses()->beforeEach(function () {
    Artisan::call('migrate:fresh', ['--database' => 'db_auth', '--path' => "database/migrations/admsys"]);
    Artisan::call('migrate:fresh', ['--database' => 'db_nomadelfia', '--path' => "database/migrations/db_nomadelfia"]);
    Artisan::call('migrate:fresh', ['--database' => 'db_scuola', '--path' => "database/migrations/scuola"]);
    Artisan::call('migrate:fresh', ['--database' => 'db_scuola', '--path' => "database/migrations/scuola"]);
    Artisan::call('migrate:fresh', ['--database' => 'db_biblioteca', '--path' => "database/migrations/biblioteca"]);
    Artisan::call('migrate:fresh', ['--database' => 'db_patente', '--path' => "database/migrations/patente"]);
    Artisan::call('migrate:fresh', ['--database' => 'db_officina', '--path' => "database/migrations/officina"]);

    Artisan::call('db:seed', ['--class' => 'LocalEnvironmentSeeder']);
})->in('Biblioteca', 'Scuola', 'Popolazione');


function login(User $user = null): User
{
    if (is_null($user)) {
        $user = User::where('username', "=", 'Admin')->first();
    }

    actingAs($user);

    return $user;
}