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

@foreach ($gruppo->personeAttuale->chunk(3) as $chunk)
    <div class="row">
        @foreach ($chunk as $p)
            <div class="col-md-4">{{ $p->nominativo }}</div>
        @endforeach
    </div>
@endforeach

    @foreach($gruppo->persone as $persona)
        @if($persona->isSingle() or $persona->isCapofamiglia())
        <div class="card my-2">
         <div class="card-body">
  
            <div class="font-weight-bold">
                <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$persona->id])}}">{{$persona->nominativo}}</a>
                @if ($persona->famigliaAttuale()->moglie())  
                <p>
                    <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$persona->famigliaAttuale()->moglie()->id])}}"> {{$persona->famigliaAttuale()->moglie()->nominativo}}</a>
                </p>
                @endif
            </div>
            <ul>
            @foreach($persona->famigliaAttuale()->figliAttuali as $figlio)
            <li>{{Carbon::parse($figlio->data_nascita)->year}}  
                <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$figlio->id])}}"> {{$figlio->nominativo}}</a>
            </li>
            @endforeach
            </ul>
        </div>
        </div>

        @else
        <!-- <p class="font-weight-bold">
          <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$persona->id])}}">{{$persona->nominativo}}</a>
        </p> -->
        @endif
    @endforeach
@endsection


