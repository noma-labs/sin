@extends('layouts.app')

@section('title', 'rtn')

@section("archivioname","film")

@section('archivio')
  <div class="list-group">
    <a href={{ url('/rtn/film')}} class="list-group-item list-group-item-action"> Film </a>
    <a href={{ url('/rtn/archprof')}} class="list-group-item active"> Archivio Professionale </a>
    <a href={{ url('/rtn/film/commissione')}} class="list-group-item "> Commissione TV </a>
  </div>
@endsection
