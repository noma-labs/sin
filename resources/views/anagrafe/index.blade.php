@extends("layouts.app")

@section("title", "Anagrafe")

@section("navbar-link")
  <li class="nav-item">
    <a class="nav-link" href="{{ route("anagrafe.index") }}">Anagrafe</a>
  </li>
  <li class="nav-item dropdown">
    <a
      class="nav-link dropdown-toggle"
      id="navbarPesone"
      role="button"
      data-bs-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false"
    >
      Persone
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarPesone">
      <a class="dropdown-item" href="{{ route("anagrafe.people.search") }}">
        Ricerca persone
      </a>
      <a class="dropdown-item" href="{{ route("anagrafe.person.create") }}">
        Inserisci Persona
      </a>
    </div>
  </li>
  <li class="nav-item dropdown">
    <a
      class="nav-link dropdown-toggle"
      id="navbarFamiglie"
      role="button"
      data-bs-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false"
    >
      Famiglie
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarFamiglie">
      <a class="dropdown-item" href="{{ route("anagrafe.families") }}">
        Gestione famiglie
      </a>
      <a class="dropdown-item" href="{{ route("anagrafe.marriage.create") }}">
        Nuovo Matrimonio
      </a>
    </div>
  </li>
@append
