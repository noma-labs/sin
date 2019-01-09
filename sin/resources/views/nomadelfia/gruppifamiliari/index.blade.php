@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Gruppi Familiari'])

@foreach($gruppifamiliari->chunk(3) as $chunk)
 <div class="row">
    @foreach ($chunk as $gruppo)
      <div class="col-md-4 my-1">
          <div id="accordion">
            <div class="card">
              <div class="card-header" id="headingOne">
                <h5 class="mb-0">
                  <button class="btn btn-link" data-toggle="collapse" data-target="#{{$gruppo->id}}" aria-expanded="false" aria-controls="collapseOne">
                  <span class="font-weight-bold"> {{$gruppo->nome}} </span>  <span class="font-weight-bold m-3">  {{$gruppo->persone->count()}}</span>
                  </button>
                </h5>
              </div>
              <div id="{{$gruppo->id}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    @if ($gruppo->capogruppoAttuale())
                    <p class="font-weight-bold"> Capogruppo: 
                        <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$gruppo->capogruppoAttuale()->id])}}">  
                      {{$gruppo->capogruppoAttuale()->nominativo}}</a>
                    </p> 
                    @else
                      <p class="text-danger">Senza capogruppo</p> 
                    @endif
                    <!-- componenti gruppi per persone -->
                    @foreach($gruppo->persone as $persona)
                      @if($persona->single())
                        <p><a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$persona->id])}}">{{$persona->nominativo}}</a></p>
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
                      <div class="row">
                          <a class="btn btn-dangercol-md-4 offset-md-6" type="button" href="{{ route('nomadelfia.gruppifamiliari.modifica', $gruppo->id)}}">Modifica</a>
                      </div>
                </div>    
              </div>
            </div>
          </div>
      </div>      
     @endforeach
 </div> 
@endforeach

@endsection
