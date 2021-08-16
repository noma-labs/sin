@extends('layouts.app')

@section('title', 'Gestione Scuola')

@section('navbar-link')
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarScuola" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Scuola
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarScuola">
            <a class="dropdown-item" href="{{route('scuola.classi')}}"> Anno Scolastico</a>
        </div>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarScuolaClasse" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Classi
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarScuolaClasse">
            <a class="dropdown-item" href="{{route('scuola.tipi')}}"> Gestione Tipo Calsse</a>
        </div>
    </li>
@append

