<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

final class UsersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $password;

        return [
            'name' => 'Admin',
            'email' => 'archivio@nomadelfia.it',
            'password' => $password ?: $password = bcrypt('admin'),
            'remember_token' => str_random(100),
        ];
    }
}
