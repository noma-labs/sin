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
<<<<<<< HEAD
             'scadute'=>null
=======
            'scadute'=>30
>>>>>>> 25438b1a95f93eecc3951de9b88db67a19c7e1f1
        ],
    ],

];
