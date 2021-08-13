@extends('layouts.app')

@section('title', 'Gestione Scuola')

@section('navbar-link')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('scuola') }}">Classi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('scuola') }}">Alunni</a>
    </li>
@append

