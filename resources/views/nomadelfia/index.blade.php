@extends("layouts.app")

@section("title", "Popolazione")

@section("navbar-link")
  <li class="nav-item">
    <a class="nav-link" href="{{ route("anagrafe.index") }}">Persone</a>
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
