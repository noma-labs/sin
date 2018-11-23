<?php

return [
      /*
    |--------------------------------------------------------------------------
    | Configurazione per il sistema patente
    |--------------------------------------------------------------------------
    |
    | 
    */
    
    /*
    |--------------------------------------------------------------------------
    |  Scadenze
    |--------------------------------------------------------------------------
    |  Imposta i giorni entro il quale visualizzare le patenti in scadenza e quelle già scadute.
    */
    'scadenze' =>[
        'patenti'=> [
            'inscadenza'=> 30,
            'scadute'=> 30
        ],
        'commissione'=> [
            'inscadenza'=> 90,
            'scadute'=> 30
        ],
        'cqc'=> [
            'inscadenza'=>90,
            'scadute'=>30
        ],
    ],

];
