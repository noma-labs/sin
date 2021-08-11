<?php

use App\Nomadelfia\Models\GruppoFamiliare;
use Faker\Generator as Faker;

$factory->define(GruppoFamiliare::class, function (Faker $faker) {
    $nome = $faker->lastName;
    return [
        'nome' => $nome
    ];
});
