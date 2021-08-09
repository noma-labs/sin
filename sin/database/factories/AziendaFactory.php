<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

use App\Nomadelfia\Models\Azienda;

$factory->define(Azienda::class, function (Faker $faker) {
    $name = $faker->firstName;
    return [
        'nome_azienda'=>$name,
        'descrizione_azienda'=>"a description",
        'data_azienda'=>$faker->date,
        'tipo'=>'azienda',
    ];
});

$factory->state(Azienda::class, 'ufficio', [
    'tipo' => "ufficio",
]);
