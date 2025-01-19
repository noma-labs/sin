<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8" />
        <title>@yield("title")</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <link rel="stylesheet" href="{{ asset("css/app.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/sin-theme.css") }}" />
    </head>
    <body>
        <div id="app">
            <div class="sticky-top">
                <nav
                    class="navbar navbar-expand-md navbar-inverse bg-inverse"
                    style="background-color: white"
                >
                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url("/home") }}">
                        <img
                            style="
                                display: inline-block;
                                height: 40px;
                                margin-top: -5px;
                            "
                            src="/images/logo-noma.png"
                        />
                    </a>
                    <button
                        class="navbar-toggler"
                        type="button"
                        data-toggle="collapse"
                        data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                    >
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div
                        class="collapse navbar-collapse"
                        id="navbarSupportedContent"
                    >
                        <ul class="navbar-nav mr-auto">
                            @yield("navbar-link")
                        </ul>
                        <!-- Right Side Of Navbar -->
                        <ul class="nav navbar-nav ml-auto">
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
                                    <a
                                        class="nav-link"
                                        href="{{ route("home") }}"
                                    >
                                        Entra come ospite
                                    </a>
                                </li>
                            @else
                                <li class="nav-item dropdown">
                                    <a
                                        href="#"
                                        class="nav-link dropdown-toggle"
                                        role="button"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    >
                                        {{ Auth::user()->username }}
                                        <span class="caret"></span>
                                    </a>

                                    <ul
                                        class="dropdown-menu dropdown-menu-right"
                                        role="menu"
                                    >
                                        <li>
                                            @role("admin")
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route("admin.backup") }}"
                                                >
                                                    Pannello Amministazione
                                                </a>
                                            @endrole

                                            <a
                                                class="dropdown-item"
                                                href="{{ route("logout") }}"
                                                onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();"
                                            >
                                                Logout
                                            </a>

                                            <form
                                                id="logout-form"
                                                action="{{ route("logout") }}"
                                                method="POST"
                                                style="display: none"
                                            >
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="container-fluid">
                @include("partials.flash")
                @include("partials.errors")
                @yield("content")
            </div>
        </div>

        <script type="text/javascript" src="{{ asset("js/app.js") }}"></script>
    </body>
</html>
