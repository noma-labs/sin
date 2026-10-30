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
    <a class="nav-link" href="{{ route("anagrafe.families") }}">Famiglie</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.gruppifamiliari") }}">
      Gruppi familiari
    </a>
  </li>
  @can("popolazione.persona.visualizza")
    <li class="nav-item">
      <a class="nav-link" href="{{ route("nomadelfia.ricorrenze") }}">
        Ricorrenze
      </a>
    </li>
  @endcan
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

@section("navbar-search")
  @can("popolazione.persona.visualizza")
    <li class="nav-item">
      <form
        class="d-flex me-md-2"
        role="search"
        method="GET"
        action="{{ route("anagrafe.people.search.show") }}"
      >
        <input
          class="form-control me-2"
          type="search"
          name="q"
          value="{{ request("q") }}"
          placeholder="Nominativo, nome, cognome o numero elenco"
          aria-label="Cerca persona"
          required
        />
        <button class="btn btn-outline-primary" type="submit">Cerca</button>
      </form>
    </li>
  @endcan
@endsection
