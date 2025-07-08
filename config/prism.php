<?php

return [
    'prism_server' => [
        // The middleware that will be applied to the Prism Server routes.
        'middleware' => [],
        'enabled' => env('PRISM_SERVER_ENABLED', false),
    ],
    'providers' => [
        'ollama' => [
            'url' => env('OLLAMA_URL', 'http://ollama:11434'),
        ],
    ],
];
