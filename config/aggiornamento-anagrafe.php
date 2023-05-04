<?php

use Illuminate\Support\Str;

return [

    /*
    * If set to false, no activities will be saved to the database.
    */
    'enabled' => env('AGGIORNAMENTO_ANAGRAFE_ENABLED', true),

    /*
    * If no mailer is passed  we use this default mailer to send the mails.
    */
    'default_mailer' => 'default',

    /*
     * List of recipients to send the aggiornamento anagrafe emails
     */
    'to' => Str::of(env('AGGIORNAMENTO_ANAGRAFE_TO', 'test@nomadelfia.it'))->split('/[\s,]+/')->toArray(),

];
