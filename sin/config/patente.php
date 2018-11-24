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
            'scadute'=> null // if null tutte le patenti scadeute
        ],
        'commissione'=> [
            'inscadenza'=> 90,
            'scadute'=> null
        ],
        'cqc'=> [
            'inscadenza'=>90,
             'scadute'=>null
        ],
    ],

];