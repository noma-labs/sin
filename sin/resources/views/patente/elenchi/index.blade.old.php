<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS style (boostrap, jquery-ui) compiled with "npm run prod"  -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" >

    <style type="text/css">
        div.page
        {
            page-break-after: always;
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <div class="container">
       <div class="page">
            @include('patente.elenchi.dichiarazione')
        </div>

        <div class="page">
            @include('patente.elenchi.autorizzati')
        </div>
    </div>

   <!-- JS compiled with Laravel-mix -->
   <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
   <!-- CSS archivio -->
   <link rel="stylesheet" href="{{ asset('css/archivio/archivio.css') }}" >
</body>

</html>
