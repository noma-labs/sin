<?php

use App\Nomadelfia\Models\Famiglia;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Famiglia::class, function (Faker\Generator $faker) {
    return [
        'nome_famiglia'=> $this->faker->name, 
        'data_creazione'=> $this->faker->date,
    ];
});
