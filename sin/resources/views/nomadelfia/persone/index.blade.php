@extends('nomadelfia.index')

@section('navbar-link')
  <li class="nav-item">
      <a class="nav-link" href="{{ route('nomadelfia') }}">Nomadelfia</a>
  </li>
  <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Persone
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{route('persone.inserimento')}}">Inserisci Persona</a>
          <a class="dropdown-item" href="{{route('nomadelfia.autocomplete.persona')}}">Ricerca Persona</a>
        </div>
  </li>
@endsection

