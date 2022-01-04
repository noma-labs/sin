<?php

use App\Nomadelfia\Models\Incarico;
use Faker\Generator as Faker;


$factory->define(Incarico::class, function (Faker $faker) {
    $name = $faker->firstName;
    return [
        'nome'=>$name,
        'descrizione'=>"a description",
    ];
});

$factory->state(Incarico::class, 'incarico', [
    'tipo' => "incarico",
]);

