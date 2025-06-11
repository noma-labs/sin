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
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li>
                <a class="dropdown-item" href="{{ route("veicoli.index") }}">
                    Lista Veicoli
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route("veicoli.create") }}">
                    Nuovo Veicolo
                </a>
            </li>
            <li><div class="dropdown-divider"></div></li>
            <li>
                <a
                    class="dropdown-item"
                    href="{{ route("veicoli.demoliti") }}"
                >
                    Veicoli Demoliti
                </a>
            </li>
        </ul>
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
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li>
                <a class="dropdown-item" href="{{ route("filtri") }}">
                    Filtri
                </a>
            </li>
            <li><a class="dropdown-item" href="">Gomme</a></li>
            <li><a class="dropdown-item" href="">Modelli</a></li>
            <li><a class="dropdown-item" href="">Marche</a></li>
            <l><a class="dropdown-item" href="">Oli Motore</a></l>
        </ul>
    </li>
@endsection
