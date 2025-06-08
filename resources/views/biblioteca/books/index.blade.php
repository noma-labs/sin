@extends("biblioteca.index")

@section("navbar-link")
@parent
<li class="nav-item dropdown">
    <a
        class="nav-link dropdown-toggle"
        id="navbarDropdown"
        role="button"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        Libri
    </a>

    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="{{ route("books.index") }}">
            Ricerca libro
        </a>
        @if (! Auth::guest())
            <a class="dropdown-item" href="{{ route("books.create") }}">
                Aggiungi libro
            </a>
            <a class="dropdown-item" href="{{ route("audience.index") }}">
                Classificazioni
            </a>
            <a class="dropdown-item" href="{{ route("books.labels") }}">
                Etichette
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route("books.trashed") }}">
                Libri eliminati
            </a>
        @endif
    </div>
</li>

@if (! Auth::guest())
    <li class="nav-item dropdown" aria-labelledby="navbarDropdown">
        <a
            class="nav-link dropdown-toggle"
            href="{{ route("authors.index") }}"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Autori
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route("authors.index") }}">
                Visualizza
            </a>
            <a class="dropdown-item" href="{{ route("authors.create") }}">
                Aggiungi
            </a>
        </div>
    </li>
    <li class="nav-item dropdown">
        <a
            class="nav-link dropdown-toggle"
            href="{{ route("editors.index") }}"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Editori
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route("editors.index") }}">
                Visualizza
            </a>
            <a class="dropdown-item" href="{{ route("editors.create") }}">
                Aggiungi
            </a>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route("books.loans") }}">Prestiti</a>
    </li>
@endif

@append
