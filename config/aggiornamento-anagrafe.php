<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [

    /*
    * If set to false, no emails are sent after an aggiornamento anagrafe is performed
    */
    'enabled' => env('AGGIORNAMENTO_ANAGRAFE_ENABLED', true),

    /*
    * If no mailer is passed  we use this default mailer to send the mails.
    */
    'default_mailer' => 'default',

    /*
     * Main recipients that receive the aggiornamento anagrafe emails
     */
    'to' => env('AGGIORNAMENTO_ANAGRAFE_TO', 'test@nomadelfia.it'),

    /*
     * List of CC recipients that receive the aggiornamento anagrafe emails (separated by comma)
     */
    'cc' => Str::of(env('AGGIORNAMENTO_ANAGRAFE_CC'))->split('/[\s,]+/', -1, PREG_SPLIT_NO_EMPTY)->toArray(),

];
