@extends('officina.index')

@section('title', 'Filtri')

@section("archivio")
@include('partials.header', ['title' => 'Gestione Filtri'])
    <gestione-filtri></gestione-filtri>
@endsection