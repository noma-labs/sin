@extends('layouts.app')

@section('title', 'Gestione Scuola')

@section('navbar-link')
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarScuola" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Scuola
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarScuola">
            <a class="dropdown-item" href="{{route('scuola')}}">Anno Scolastico</a>
            <a class="dropdown-item" href="{{route('scuola.classi')}}"> Classi</a>
        </div>
    </li>
@append

