@extends("layouts.app")

@section("title", "Agraria")

@section("navbar-link")
<li class="nav-item">
    <a class="nav-link" href="{{ route("agraria.index") }}">Agraria</a>
</li>
<li class="nav-item dropdown">
    <a
        class="nav-link dropdown-toggle"
        id="manDropdown"
        role="button"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        Manutenzione
    </a>
    <div class="dropdown-menu" aria-labelledby="manDropdown">
        <a
            class="dropdown-item"
            href="{{ route("agraria.maintenanace.search.show") }}"
        >
            Ricerca
        </a>
        <a
            class="dropdown-item"
            href="{{ route("agraria.maintenanace.create") }}"
        >
            Inserisci
        </a>
        <a
            class="dropdown-item"
            href="{{ route("agraria.maintenanace.planned.index") }}"
        >
            Programmate
        </a>
    </div>
</li>
<li class="nav-item dropdown">
    <a
        class="nav-link dropdown-toggle"
        href="#"
        id="tratDropdown"
        role="button"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        Mezzo Agricolo
    </a>
    <div class="dropdown-menu" aria-labelledby="tratDropdown">
        <a class="dropdown-item" href="{{ route("agraria.vehichles.index") }}">
            Elenco
        </a>
        <a class="dropdown-item" href="{{ route("agraria.vehicle.create") }}">
            Inserisci
        </a>
    </div>
</li>
@append
