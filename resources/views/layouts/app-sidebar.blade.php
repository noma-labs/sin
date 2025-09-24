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
        <header class="navbar sticky-top flex-md-nowrap p-0">
            <a
                class="navbar-brand col-md-3 col-lg-2 me-0 px-3"
                href="{{ url("/home") }}"
            >
                <img src="/images/logo-noma.png" alt="" height="40" />
            </a>
            <button
                class="navbar-toggler position-absolute d-md-none collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#sidebarMenu"
                aria-controls="sidebarMenu"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            {{-- <input
                class="form-control form-control w-100"
                type="text"
                placeholder="Search"
                aria-label="Search"
            /> --}}
            <div class="navbar-nav">
                <div class="nav-item text-nowrap">
                    <a class="nav-link px-3" href="#">Sign out</a>
                </div>
            </div>
        </header>

        <div class="container-fluid">
            <div class="row">
                <nav
                    id="sidebarMenu"
                    class="col-md-2 col-lg-2 d-md-block bg-light sidebar collapse"
                >
                    <div class="position-sticky pt-3">
                         @yield("navbar-link")
                    </div>
                </nav>

                <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
                     @yield("content")
                </main>
            </div>
        </div>

        <script type="text/javascript" src="{{ asset("js/app.js") }}"></script>
    </body>
</html>
