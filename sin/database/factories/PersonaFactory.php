<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

use App\Nomadelfia\Models\Persona;

$factory->define(Persona::class, function (Faker $faker) {
    $name = $faker->firstName;
    $surname = $faker->lastName;
    return [
        'nominativo'=>$name." ".Str::substr($surname, 0, 1), 
        'sesso'=>"M",
        'nome'=>$name,
        "cognome"=>$surname,
        "provincia_nascita"=>"GR",
        'data_nascita'=>$faker->date,
        // TODO: delete this column because the categoria is a many to many relationship in to the persone_categoria table
        'categoria_id' =>1,
        'id_arch_pietro'=>0,
        'id_arch_enrico'=>0
    ];
});

$factory->state(Persona::class, 'femmina', [
    'sesso' => "F",
]);

$factory->state(Persona::class, 'maschio', [
    'sesso' => "M",
]);

$factory->state(Persona::class, 'minorenne', [
    'data_nascita' => Carbon::now()->subYears(10)->toDateString(),
]);

$factory->state(Persona::class, 'maggiorenne', [
    'data_nascita' => Carbon::now()->subYears(30)->toDateString(),
]);