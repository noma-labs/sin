@extends("layouts.app")

@section("title", "Agraria")

@section("navbar-link")
<li class="nav-item">
    <a class="nav-link" href="{{ route("agraria.index") }}">Agraria</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("agraria.vehichles.index") }}">
        Mezzi Agricoli
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("agraria.maintenanace.search.show") }}">
        Manutenzioni
    </a>
</li>
<li class="nav-item">
    <a
        class="nav-link"
        href="{{ route("agraria.maintenanace.planned.index") }}"
    >
        Manutenzioni Programmate
    </a>
</li>

@append
