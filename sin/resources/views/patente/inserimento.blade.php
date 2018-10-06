@extends('patente.index')

@section('archivio')
   
<sin-header title="Inserisci nuova Patente"> </sin-header>

<patente-inserimento
            api-patente-persone="{{route('api.patente.persone')}}"
            api-patente-categorie="{{route('api.patente.categorie')}}"
            api-patente-create="{{route('api.patente.create')}}" >
</patente-inserimento>

@endsection
