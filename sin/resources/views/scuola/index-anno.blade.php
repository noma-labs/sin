@extends('scuola.index')

@section('title', 'Gestione Scuola')

@section('navbar-link')
    @parent
    <li class="nav-item">
        <a class="nav-link" href="{{ route('scuola.classi') }}">Classi</a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="{{ route('scuola.elaborati') }}" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Elaborati
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{route('scuola.elaborati.insert')}}">Aggiungi</a>
            <a class="dropdown-item" href="{{ route('scuola.elaborati') }}">Ricerca</a>
        </div>
    </li>
@endsection

