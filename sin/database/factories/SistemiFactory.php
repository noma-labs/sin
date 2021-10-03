<?php

use App\Admin\Models\Sistema;
//use App\Admin\Models\Risorsa;

$factory->define(Sistema::class, function () {
    return [
        'nome' => "nomadelfia",
        'descrizione' => "gestione popolazione",
    ];
});


