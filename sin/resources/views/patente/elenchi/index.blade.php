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
        table td{
                color:#000000;
		font-size: 65%;
		font-stretch: expanded;
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
