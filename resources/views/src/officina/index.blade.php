@extends("layouts.app")

@section("title", "officina")

@section("navbar-link")
    <li class="nav-item">
        <a
            href="{{ route("officina.index", ["giorno" => "oggi"]) }}"
            class="nav-link"
        >
            Officina
        </a>
    </li>

    <li class="nav-item dropdown">
        <a
            class="nav-link dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Prenotazioni
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route("officina.index") }}">
                Aggiungi prenotazione
            </a>
            <a class="dropdown-item" href="{{ route("officina.ricerca") }}">
                Ricerca prenotazioni
            </a>
        </div>
    </li>
    <li class="nav-item dropdown">
        <a
            class="nav-link dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Veicoli
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route("veicoli.index") }}">
                Lista Veicoli
            </a>
            <a class="dropdown-item" href="{{ route("veicoli.nuovo") }}">
                Nuovo Veicolo
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route("veicoli.demoliti") }}">
                Veicoli Demoliti
            </a>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route("officina.patenti") }}">
            Elenco Patenti
        </a>
    </li>
    <li class="nav-item dropdown">
        <a
            class="nav-link dropdown-toggle"
            href="#"
            id="navbarDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Gestione
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route("filtri") }}">Filtri</a>
            <a class="dropdown-item" href="">Gomme</a>
            <a class="dropdown-item" href="">Modelli</a>
            <a class="dropdown-item" href="">Marche</a>
            <a class="dropdown-item" href="">Oli Motore</a>
        </div>
    </li>
@endsection
