@extends("layouts.app")

@section("title", "Archivio Documenti")

@section("navbar-link")
    <li class="nav-item dropdown">
        <a
            class="nav-link dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Archivio Libri
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route("archiviodocumenti") }}">
                Ricerca libri
            </a>
            <a
                class="dropdown-item"
                href="{{ route("archiviodocumenti.etichette") }}"
            >
                Gestione stampa etichette
            </a>
        </div>
    </li>
@endsection
