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
        <nav class="navbar sticky-top bg-body-tertiary navbar-expand-md">
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
                            <li class="nav-item dropdown dropdown-center">
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

        <script type="text/javascript" src="{{ asset("js/app.js") }}"></script>
    </body>
</html>
