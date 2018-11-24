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
    |  Se null non 
    */
    'scadenze' =>[
        'patenti'=> [
            'inscadenza'=> 30,
            'scadute'=> null // number:  if null tutte le patenti scadute
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
