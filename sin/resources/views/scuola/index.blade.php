@extends('layouts.app')

@section('title', 'Gestione Scuola')

@section('navbar-link')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('scuola') }}">Anno Scolastico</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('scuola.classi') }}">Classi</a>
    </li>
@append

