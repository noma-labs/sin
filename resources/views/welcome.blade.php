<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link
            rel="icon"
            href="{{ asset("/images/noma.png") }}"
            type="image/png"
        />

        <title>Nomadelfia</title>

        <style>
            html,
            body {
                background-color: #fff;
                color: #28519f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .c-y {
                color: #f9b234;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .top-left {
                position: absolute;
                left: 10px;
                top: 18px;
            }
            .content {
                text-align: center;
            }

            .title {
                font-size: 70px;
            }

            .links > a {
                color: #28519f;
                padding: 0 25px;
                font-size: 15px;
                font-weight: 600;
                letter-spacing: 0.1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="top-left">
                <img
                    src="{{ asset("images/logo-nta-big.png") }}"
                    alt="Nomadelfia Logo"
                    width="150"
                />
            </div>
            @if (Route::has("login"))
                <div class="top-right links">
                    @auth
                        <a href="{{ url("/home") }}">Entra</a>
                    @else
                        <a href="{{ route("login") }}">Autenticati</a>
                        <a href="{{ route("home") }}">Sessione ospite</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    <span class="c-y">S</span>
                    istema
                    <span class="c-y">I</span>
                    nformativo
                    <span class="c-y">N</span>
                    omadelfia
                </div>
            </div>
        </div>
    </body>
</html>
