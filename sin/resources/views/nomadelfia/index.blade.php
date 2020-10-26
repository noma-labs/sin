@extends('layouts.app')

@section('title', 'Gestione Nomadelfia')

@section('navbar-link')
  <li class="nav-item">
      <a class="nav-link" href="{{ route('nomadelfia') }}">Nomadelfia</a>
  </li>
  
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle"  id="navbarPesone" role="button" 
    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Persone
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarPesone">
      <a class="dropdown-item" href="{{ route('nomadelfia.persone') }}">Visualizza</a> 
      <a class="dropdown-item" href="{{ route('nomadelfia.persone.ricerca') }}" >Ricerca persone</a> 
      <a class="dropdown-item" href="{{ route('nomadelfia.persone.inserimento')}}">Inserisci Persona</a>
    </div>
  </li>
 
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle"  id="navbarFamiglie" role="button" 
      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Famiglie
      </a>
      <div class="dropdown-menu" aria-labelledby="navbarFamiglie">
        <a class="dropdown-item" href="{{ route('nomadelfia.famiglie') }}">Gestione famiglie</a> 
        {{-- <a class="dropdown-item" href="{{route('nomadelfia.famiglie.create')}}">Inserisci Famiglia</a> --}}
      </div>
  </li>
  <li class="nav-item">
     <a class="nav-link"href="{{ route('nomadelfia.gruppifamiliari') }}">Gruppi familiari</a>
  </li>
  <li class="nav-item">
     <a class="nav-link"  href="{{ route('nomadelfia.aziende') }}">Aziende</a>
  </li>
@append 

