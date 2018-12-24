@extends('layouts.app')

@section('title', 'officina')

@section('navbar-link')
  <li class='nav-item'><a href="{{ route('officina.index') }}" class="nav-link">Officina</a> </li>
  <!-- <li class='nav-item'><a href="{{ route('officina.index') }}" class="nav-link">Prenotazioni</a> </li> -->

  <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Prenotazioni
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{route('officina.index')}}">Aggiungi prenotazione</a>
          <a class="dropdown-item" href="{{route('officina.ricerca')}}">Ricerca prenotazioni</a>
        </div>
  </li>
  <li class='nav-item dropdown'> 
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Veicoli </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
      <a class="dropdown-item" href="{{route('veicoli.index')}}">Lista Veicoli</a>
      <a class="dropdown-item" href="{{route('veicoli.nuovo')}}">Nuovo Veicolo</a>
    </div>
  </li>
  <li class="nav-item">
        <a class="nav-link"  href="{{route('officina.patenti')}}" >Elenco Patenti</a>
  </li>
@endsection
