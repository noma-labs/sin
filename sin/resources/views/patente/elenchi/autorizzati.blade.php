@extends('patente.index')

@section('archivio')

    @foreach ( $patentiAutorizzati as $patente)
      <div>{{$patente->numero_patente}}</div>
    @endforeach
@endsection
