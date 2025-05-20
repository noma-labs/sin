@extends("layouts.app")

@section("title", "Foto")

@section("navbar-link")
<li class="nav-item">
    <a class="nav-link" href="{{ route("photos.index") }}">Tutte</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("photos.favorite.index") }}">MostraFotografica</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("photos.face.index") }}">Persone</a>
</li>
@append
