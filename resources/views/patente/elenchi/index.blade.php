<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" >
        <style type="text/css">
        div.page
        {
            page-break-before: always; 
            page-break-inside: avoid;
        }
        td {
            font-size: 10px;
        }
        </style>
    </head>

    <body>
        <div class="page">
            @include('patente.elenchi.dichiarazione')
        </div>

        <div class="page">
            @include('patente.elenchi.autorizzati')
        </div>
    </body>
</html>
