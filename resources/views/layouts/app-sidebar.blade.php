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
    <body class="overflow-x-hidden">
        <div class="container-fluid overflow-y-hidden">
            <div class="row">
                <nav
                    class="navbar col-md-2 col-lg-2 bg-light ">
                >
                     <a
                        class="navbar-brand"
                        href="{{ url("/home") }}"
                    >
                        <img src="/images/logo-noma.png" alt="The logo of the application" class="img-fluid" />
                    </a>
                    <div class="position-sticky pt-3">
                         @yield("navbar-link")
                    </div>
                </nav>
                <main class="col-md-10 col-lg-10">
                     @yield("content")
                </main>
            </div>
        </div>
    </body>

    <script type="text/javascript" src="{{ asset("js/app.js") }}"></script>
</html>
