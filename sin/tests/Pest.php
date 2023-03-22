<?php

use App\Admin\Models\User;
use Illuminate\Support\Facades\Artisan;

use Tests\TestCasePest;
use function Pest\Laravel\actingAs;


uses(TestCasePest::class)->in('Biblioteca', 'Scuola', 'Popolazione');


function login(User $user = null): User
{
    if (is_null($user)) {
        $user = User::where('username', "=", 'Admin')->first();
    }

    actingAs($user);

    return $user;
}