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
        id="navbarProblemi"
        role="button"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        Problemi
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarProblemi">
        <a
            class="dropdown-item"
            href="{{ route("photos.issues.index") }}"
        >
            Problemi aperti
        </a>
        <a
            class="dropdown-item"
            href="{{ route("photos.issues.resolved") }}"
        >
            Problemi risolti
        </a>
    </div>
</li>
@append
