<?php

declare(strict_types=1);

return [

    'grafana' => [
        'base_url' => env('STAZIONE_METEO_URL', 'http://192.168.11.7:3000/dashboard/snapshot/40CbOLWT3NSNdc62Pm6KZzmyLt4Fk6t6'),
        'temp_panel_url' => 'http://192.168.11.7:3000/d-solo/z-qyiG1Mk/weather?orgId=1&from=1648669138310&to=1648755538310&panelId=12',
        'anem_panel_url' => 'http://192.168.11.7:3000/d-solo/z-qyiG1Mk/weather?orgId=1&from=1648669497079&to=1648755897079&panelId=13',
    ],
];
