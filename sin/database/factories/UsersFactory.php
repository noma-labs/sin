<?php

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
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