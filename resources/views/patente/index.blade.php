@extends("layouts.app")

@section("title", "Patente")

@section("navbar-link")
<li class="nav-item dropdown">
    <a
        class="nav-link dropdown-toggle"
        id="navbarDropdown"
        role="button"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        Patenti
    </a>

    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="{{ route("patente.scadenze") }}">
            Scadenze patenti
        </a>
        @can("scuolaguida.inserisci")
            <a class="dropdown-item" href="{{ route("patente.create") }}">
                Aggiungi patente
            </a>
        @endcan

        <a class="dropdown-item" href="{{ route("patente.ricerca") }}">
            Ricerca patente
        </a>
    </div>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("patente.elenchi") }}">
        Elenchi patenti
    </a>
</li>
@append
