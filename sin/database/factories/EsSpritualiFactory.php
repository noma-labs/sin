<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

use App\Nomadelfia\Models\EserciziSpirituali;
use App\Nomadelfia\Models\Persona;

$factory->define(EserciziSpirituali::class, function (Faker $faker) {
    $resp =  factory(Persona::class)->states("maggiorenne", "maschio")->create();
    return [
        'turno'=>"1-turno",
        'responsabile_id'=>$resp->id,
        'data_inizio'=>$faker->date,
        "data_fine"=>$faker->date,
        "luogo"=>$faker->city,
        'stato'=>'1',
    ];
});


$factory->state(EserciziSpirituali::class, '1-turno', [
    'turno' => "1-turno",
]);

$factory->state(EserciziSpirituali::class, 'disattivo', [
    'stato' => "0",
]);
