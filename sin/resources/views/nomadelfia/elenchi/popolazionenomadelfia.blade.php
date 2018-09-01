<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Elenco</title>
    <link rel="icon" href="/images/noma.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS style (boostrap, jquery-ui) compiled with "npm run prod"  -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> -->
</head>

<body>
    <div class="container">
        <h1> Popolazione di Nomadelfia</h1>
        <div class="page-break"></div>
        @include('nomadelfia.elenchi.maggiorenni')
        <div class="page-break"></div>
        @include('nomadelfia.elenchi.minorenni')
        <div class="page-break"></div>
        @include('nomadelfia.elenchi.effsacmav')
        <div class="page-break"></div>
        @include('nomadelfia.elenchi.postospfigl')
        <div class="page-break"></div>
        @include('nomadelfia.elenchi.famiglie')
        <div class="page-break"></div>
        @include('nomadelfia.elenchi.gruppifamiliari')
        <div class="page-break"></div>
        @include('nomadelfia.elenchi.scuola')
        <div class="page-break"></div>
        @include('nomadelfia.elenchi.aziende')
        <div class="page-break"></div>
        @include('nomadelfia.elenchi.riassunto')
    </div>
   <!-- JS compiled with Laravel-mix -->
   <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
   <!-- CSS archivio -->
   <link rel="stylesheet" href="{{ asset('css/archivio/archivio.css') }}" >
</body>

</html>
