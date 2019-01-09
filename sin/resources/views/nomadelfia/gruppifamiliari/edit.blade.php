@extends('nomadelfia.index')

@section('archivio')
@include('partials.header', ['title' => 'Modifica Gruppo familiare'])

{{$gruppo->nome}}
@if ($gruppo->capogruppoAttuale())
<p class="font-weight-bold"> Capogruppo: 
    <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$gruppo->capogruppoAttuale()->id])}}">  
    {{$gruppo->capogruppoAttuale()->nominativo}}</a>
</p> 
@else
<p class="text-danger">Capogruppo non assegnato</p> 
@endif
@endsection