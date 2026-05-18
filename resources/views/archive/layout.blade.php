@extends("layouts.app")

@section("title", "Archivio Documenti")

@section("navbar-link")
    <li class="nav-item">
        <a class="nav-link" href="{{ route("docs.index") }}">
            Archivio
        </a>
    </li>
     <li class="nav-item">
        <a class="nav-link" href="{{ route("docs.search") }}">
            Ricerca
        </a>
    </li>
@endsection
