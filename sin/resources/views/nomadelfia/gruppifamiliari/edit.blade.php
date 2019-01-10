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
    @foreach($gruppo->persone as $persona)
        @if($persona->single())
        <p>
            <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$persona->id])}}">{{$persona->nominativo}}</a>
           
        </p>
        @elseif($persona->capofamiglia())
        <div class="font-weight-bold mt-3">
            <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$persona->id])}}"> {{$persona->nominativo}}</a>
        </div>
        @if($persona->famigliaAttuale())
            <div class="font-weight-bold">
            @if ($persona->famigliaAttuale()->moglie())  
                <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$persona->famigliaAttuale()->moglie()->id])}}"> {{$persona->famigliaAttuale()->moglie()->nominativo}}</a>
            @endif
            </div>
            <ul>
            @foreach($persona->famigliaAttuale()->figliAttuali as $figlio)
            <li>{{Carbon::parse($figlio->data_nascita)->year}}  
                <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$figlio->id])}}"> {{$figlio->nominativo}}</a>
            </li>
            @endforeach
            </ul>
            @endif
        @endif
    @endforeach
@endsection


