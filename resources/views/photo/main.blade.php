@extends("layouts.app")

@section("title", "Foto")

@section("navbar-link")
  <li class="nav-item">
    <a class="nav-link" href="{{ route("photos.index") }}">Foto</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route("photos.folders.index") }}">Cartelle</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route("photos.stripes.index") }}">Strisce</a>
  </li>
  {{--
    <li class="nav-item">
    <a class="nav-link" href="{{ route("photos.favorite.index") }}">
    MostraFotografica
    </a>
    </li>
--}}
  <li class="nav-item">
    <a class="nav-link" href="{{ route("photos.face.index") }}">Persone</a>
  </li>
  <li class="nav-item dropdown">
    <a
      class="nav-link dropdown-toggle"
      href="#"
      role="button"
      data-bs-toggle="dropdown"
      aria-expanded="false"
    >
      Problemi
    </a>
    <ul class="dropdown-menu">
      <li>
        <a class="dropdown-item" href="{{ route("photos.issues.index") }}">
          Per foto
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="{{ route("photos.issues.bulk.index") }}">
          Per striscia
        </a>
      </li>
    </ul>
  </li>
@append
