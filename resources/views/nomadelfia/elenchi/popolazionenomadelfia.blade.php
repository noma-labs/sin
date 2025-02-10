<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Elenco</title>
        <link rel="icon" href="/images/noma.png" type="image/png" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <!-- CSS style (boostrap) compiled with "npm run prod"  -->
        <link
            rel="stylesheet"
            type="text/css"
            href="{{ asset("css/app.css") }}"
        />
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> -->
        <style type="text/css">
            div.page {
                page-break-after: always;
                page-break-inside: avoid;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="page">
                @include("nomadelfia.elenchi.header")
            </div>

            @isset($maggiorenni)
                <div class="page">
                    @include("nomadelfia.elenchi.maggiorenni")
                </div>
            @endisset

            @isset($minorenni)
                <div class="page">
                    @include("nomadelfia.elenchi.minorenni")
                </div>
            @endisset

            @isset($personestati)
                <div class="page">
                    @include("nomadelfia.elenchi.effsacmav")
                </div>
            @endisset

            @isset($personeposizioni)
                <div class="page">
                    @include("nomadelfia.elenchi.postospfigl")
                </div>
            @endisset

            @isset($famiglie)
                <div class="page">
                    @include("nomadelfia.elenchi.famiglie")
                </div>
            @endif

            @isset($gruppifamiliari)
                <div class="page">
                    @include("nomadelfia.elenchi.gruppifamiliari")
                </div>
            @endif

            @isset($scuola)
                <div class="page">
                    @include("nomadelfia.elenchi.scuola")
                </div>
            @endif

            @isset($aziende)
                <div class="page">
                    @include("nomadelfia.elenchi.aziende")
                </div>
            @endif

            <div class="page">
                @include("nomadelfia.elenchi.riassunto")
            </div>
        </div>
        <!-- JS compiled with Laravel-mix -->
        <script type="text/javascript" src="{{ asset("js/app.js") }}"></script>
    </body>
</html>
