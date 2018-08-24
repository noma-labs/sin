@extends('layouts.app')

@section('title', 'rtn')

@section("archivioname","film")

@section('archivio')
  <div class="list-group">
    <a href={{ url('/rtn/film/ricerca')}} class="list-group-item list-group-item-action active"> Ricerca </a>
    <a href={{ url('/rtn/film/inserimento')}} class="list-group-item "> Inserimento </a>
    <a href={{ url('/rtn/film/modutrasm')}} class="list-group-item "> Modifica Ultimatrasmissione </a>
    <a href={{ url('/rtn/film/modifica')}} class="list-group-item "> Modifica </a>
    <a href={{ url('/rtn/film/prestiti')}} class="list-group-item "> Prestiti </a>

  </div>
@endsection
