@extends("layouts.app")

@section("title", "Popolazione")

@section("navbar-link")
  <li class="nav-item dropdown">
    <a
      class="nav-link dropdown-toggle"
      id="navbarPersone"
      role="button"
      data-bs-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false"
    >
      Persone
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarPersone">
      <a class="dropdown-item" href="{{ route("anagrafe.index") }}">
        Elenco persone
      </a>
      @can("popolazione.persona.inserisci")
        <a class="dropdown-item" href="{{ route("anagrafe.person.create") }}">
          Inserisci persona
        </a>
      @endcan
    </div>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.index") }}">Popolazione</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.gruppifamiliari") }}">
      Gruppi familiari
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.aziende") }}">Aziende</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.incarichi.index") }}">
      Incarichi
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.esercizi") }}">
      Es. Spirituali
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.cariche.index") }}">
      Cariche
    </a>
  </li>

@append
