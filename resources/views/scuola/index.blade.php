@extends("layouts.app")

@section("title", "Gestione Scuola")

@section("navbar-link")
    <li class="nav-item dropdown">
        <a
            class="nav-link dropdown-toggle"
            href="{{ route("scuola.summary") }}"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Anni scolastici
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route("scuola.summary") }}">
                Corrente
            </a>
            <a class="dropdown-item" href="{{ route("scuola.anno.storico") }}">
                Storico
            </a>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route("scuola.elaborati.index") }}">
            Elaborati
        </a>
    </li>
@endsection
