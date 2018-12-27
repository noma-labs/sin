@extends('layouts.app')

@section('title', 'Archivio Documenti')

@section('navbar-link')
  <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Archivio Documenti
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
           <a class="dropdown-item"  href="{{ route('archiviodocumenti') }}">Ricerca libri</a>
          <a class="dropdown-item" href="{{ route('archiviodocumenti.etichette') }}">Gestione stampa etichette</a>
          <div class="dropdown-divider"></div>
        </div>
  </li>
@endsection

@section('archivio')
<div class="my-page-title">
    <div class="mr-auto p-2"><span class="h1 text-center">Archivio Documenti</span></div>
</div>

@endsection
