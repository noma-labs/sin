<?php

return [
      /*
    |--------------------------------------------------------------------------
    | Configurazione della stampa delle etichette
    |--------------------------------------------------------------------------
    |
    | 
    */
    // dimesioni (in mm) della etichetta in pdf
    'dimensioni' =>[
        'larghezza'=> '30mm',
        'altezza'=>'62mm'
    ],


     /*
        |--------------------------------------------------------------------------
        |  Collocazione
        |--------------------------------------------------------------------------
        |  imposat il font-size della collocaionze dell'etichetta
        */
    'collocazione' => [
        'font-size' => "22px"
    ],
    'titolo'=> [
        'font-size' => "6px",
        'height' => "290" // altezza del div contentne il titolo
    ]
];