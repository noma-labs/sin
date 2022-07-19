@extends('layouts.app')

@section('title', 'Gestione Scuola')

@section('navbar-link')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('scuola.summary') }}">Anni scolastici</a>
    </li>
@endsection


