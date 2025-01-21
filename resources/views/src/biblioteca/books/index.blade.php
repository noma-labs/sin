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
            <a
                class="dropdown-item"
                href="{{ route("classificazioni.index") }}"
            >
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
            href="{{ route("autori.index") }}"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Autori
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route("autori.index") }}">
                Ricerca Autore
            </a>
            <a class="dropdown-item" href="{{ route("autori.create") }}">
                Aggiungi Autore
            </a>
        </div>
    </li>
    <li class="nav-item dropdown">
        <a
            class="nav-link dropdown-toggle"
            href="{{ route("editori.index") }}"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Editori
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route("editori.index") }}">
                Ricerca Editore
            </a>
            <a class="dropdown-item" href="{{ route("editori.create") }}">
                Aggiungi Editore
            </a>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route("books.loans") }}">Prestiti</a>
    </li>
@endif

@append
