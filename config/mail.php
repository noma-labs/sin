<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
   |--------------------------------------------------------------------------
   | Mailer Configurations
   |--------------------------------------------------------------------------
   |
   | Here you may configure all of the mailers used by your application plus
   | their respective settings. Several examples have been configured for
   | you and you are free to add your own as your application requires.
   |
   | Laravel supports a variety of mail "transport" drivers that can be used
   | when delivering an email. You may specify which one you're using for
   | your mailers below. You may also add additional mailers if needed.
   |
   | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
   |            "postmark", "resend", "log", "array",
   |            "failover", "roundrobin"
   |
   */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => 'C:\xampp\sendmail\sendmail -bs',
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
        ],
    ],

    /*
   |--------------------------------------------------------------------------
   | Global "From" Address
   |--------------------------------------------------------------------------
   |
   | You may wish for all e-mails sent by your application to be sent from
   | the same address. Here, you may specify a name and address that is
   | used globally for all e-mails that are sent by your application.
   |
   */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],
];
