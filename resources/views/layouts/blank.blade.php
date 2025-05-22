<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-bs-theme="light">
    <head>
        <meta charset="utf-8" />
        <title>@yield("title")</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" href="{{ asset("css/app.css") }}" />
    </head>
    <body>
        <div class="container-fluid">
            @yield("content")
        </div>
        <script type="text/javascript" src="{{ asset("js/app.js") }}"></script>
    </body>
</html>
