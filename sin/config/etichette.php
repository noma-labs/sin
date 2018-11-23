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
        |  imposta il font-size della collocaionze dell'etichetta
        */
    'collocazione' => [
        'font-size' => "22px"
    ],
    'titolo'=> [
        'font-size' => "18px",
        'height' => "193" // altezza del div contenente il titolo
    ]
];
