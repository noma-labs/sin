@extends("layouts.app")

@section("content")
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
                Gestione Autenticazione
            </a>

            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route("users.index") }}">
                    Gestione utenti
                </a>
                <a class="dropdown-item" href="{{ route("roles.index") }}">
                    Gestione ruoli
                </a>
                <a
                    class="dropdown-item"
                    href="{{ route("permissions.index") }}"
                >
                    Gestione permessi
                </a>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route("logs.index") }}">
                Gestione attività
            </a>
        </li>
    @endsection
@endsection
