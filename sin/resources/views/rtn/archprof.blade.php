@extends('layouts.app')

@section('title', 'rtn')

@section("archivioname","archprof")

@section('archivio')
  <div class="list-group">
    <a href={{ url('/rtn/archprof/ricerca')}} class="list-group-item list-group-item-action active"> Ricerca </a>
    <a href={{ url('/rtn/archprof/inserimento')}} class="list-group-item "> Inserimento </a>
    <a href={{ url('/rtn/archprof/modutrasm')}} class="list-group-item "> Modifica Ultimatrasmissione </a>
    <a href={{ url('/rtn/archprof/modifica')}} class="list-group-item "> Modifica </a>
    <a href={{ url('/rtn/archprof/prestiti')}} class="list-group-item "> Prestiti </a>

  </div>
@endsection
