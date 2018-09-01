<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Elenco</title>
    <link rel="icon" href="/images/noma.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS style (boostrap, jquery-ui) compiled with "npm run prod"  -->
    <!-- <link rel="stylesheet" type="text/css" media="print" href="{{ asset('css/app.css') }}"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        @yield('maggiorenni')
        <div class="page-break"></div>
        @yield('minorenni')
    </div>
   <!-- JS compiled with Laravel-mix -->
   <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
   <!-- CSS archivio -->
   <link rel="stylesheet" href="{{ asset('css/archivio/archivio.css') }}" >
</body>

</html>
