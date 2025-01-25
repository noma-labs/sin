<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-bs-theme="dark">
    <head>
        <meta charset="utf-8" />
        <title>@yield("title")</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


        <!-- <link rel="stylesheet" href="{{ asset("css/app.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/sin-theme.css") }}" /> -->
    </head>
    <body>
        <nav class="navbar navbar-expand-md">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url("/home") }}">
                    <img src="/images/logo-noma.png" alt="" height="40" />
                </a>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        @yield("navbar-link")
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @if (Auth::guest())
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="{{ route("login") }}"
                                >
                                    Autenticati
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route("home") }}">
                                    Entra come ospite
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a
                                    class="nav-link dropdown-toggle"
                                    href="#"
                                    id="navbarScrollingDropdown"
                                    role="button"
                                    data-bs-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                >
                                    {{ Auth::user()->username }}
                                    <span class="caret"></span>
                                </a>

                                <ul
                                    class="dropdown-menu"
                                    aria-labelledby="navbarScrollingDropdown"
                                >
                                    <li>
                                        <button
                                            class="dropdown-item"
                                            type="submit"
                                            form="logout-form"
                                        >
                                            Logout
                                        </button>

                                        <form
                                            id="logout-form"
                                            action="{{ route("logout") }}"
                                            method="POST"
                                            style="display: none"
                                        >
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid">
            @include("partials.flash")
            @include("partials.errors")
            @yield("content")
        </div>

        <!-- <script type="text/javascript" src="{{ asset("js/app.js") }}"></script> -->
        <!-- Option 1: Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    </body>
</html>
