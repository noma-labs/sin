<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
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
            <div class="row">
                <nav class="border-end border-secondary p-0 col-md-2 col-lg-2">
                   <a class="navbar-brand" href="#">
                    <img src="/docs/5.3/assets/brand/bootstrap-logo.svg" alt="Bootstrap" width="30" height="24">
                    </a>
                     <ul class="nav flex-column">
                    <li class="nav-item">
                        <a
                            class="nav-link active"
                            aria-current="page"
                            href="{{ route("scuola.summary") }}"
                        >
                            Scuola
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="{{ route("scuola.elaborati.index") }}"
                        >
                            Elaborati
                        </a>
                    </li>
                </ul>   
                    {{-- @yield("navbar-link") --}}
                </nav>
                <main class="col-md-10 col-lg-10">
                     @yield("content")
                </main>
            </div>
        </div>

        <script type="text/javascript" src="{{ asset("js/app.js") }}"></script>
    </body>
</html>
