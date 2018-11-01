@extends('patente.index')

@section('archivio')

@foreach ($gruppifamiliari->get()->chunk(4) as $chunk)
    @foreach ( $patentiAutorizzati as $patente)
      <div>
        @isset($patente->persona->datipersonali->cognome)
         {{$patente->persona->datipersonali->cognome}}
        @endisset

        @isset($patente->persona->datipersonali->nome)
        {{$patente->persona->datipersonali->nome}} 
        @endisset

        {{$patente->categorieAsString()}}
        
        @isset($patente->persona->datipersonali->data_nascita )
        {{$patente->persona->datipersonali->data_nascita}} 
        @endisset
      </div>
    @endforeach
@endsection
