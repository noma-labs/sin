@extends("layouts.app")

@section("title", "Archivio Nomadelfia")

@section("navbar-link")
    <li class="nav-item">
        <a class="nav-link" href="{{ route("archive.index") }}">
            Archivio
        </a>
    </li>
     <li class="nav-item">
        <a class="nav-link" href="{{ route("archive.search") }}">
            Ricerca
        </a>
    </li>
@endsection
