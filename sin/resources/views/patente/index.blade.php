@extends('layouts.app')

@section('title', 'Patente')

@section('navbar-link')
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="{{route('libri.ricerca')}}" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Patenti
    </a>

    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
      <a class="dropdown-item" href="{{ route('patente.inserimento') }}">Aggiungi patente</a>
    </div>
  </li>
@append
