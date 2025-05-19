@extends("layouts.app")

@section("title", "Foto")

@section("navbar-link")
<li class="nav-item">
    <a class="nav-link" href="{{ route("photos.index") }}">Foto</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("photos.favorite.index") }}">Mostra</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("photos.face.index") }}">Volti</a>
</li>
@append
