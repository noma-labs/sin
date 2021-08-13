@extends('layouts.app')

@section('title', 'Gestione Scuola')

@section('navbar-link')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('scuola') }}">Scuola</a>
    </li>
{{--    <li class="nav-item">--}}
{{--        <a class="nav-link" href="{{ route('scuola') }}">Alunni</a>--}}
{{--    </li>--}}
@append

