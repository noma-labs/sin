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
                    @if ($gruppo->capogruppiAttuali->isNotEmpty())
                    <p class="font-weight-bold"> Capogruppo: 
                        <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$gruppo->capogruppiAttuali->first()->id])}}">  
                      {{$gruppo->capogruppiAttuali->first()->nominativo}}</a>
                    </p> 
                    @else
                    <p class="text-danger">Senza capogruppo</p> 
                  @endif
                  @foreach($gruppo->famiglie as $famiglia)
                      @if ($famiglia->single->isNotEmpty())
                      <!-- <div class=""> </div> -->
                       <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$famiglia->single->first()->id])}}">{{$famiglia->single->first()->nominativo}}</a>
                      @else
                      <div class="font-weight-bold mt-3">
                          @if ($famiglia->capofamiglia->isNotEmpty()) 
                          <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$famiglia->capofamiglia()->first()->id])}}">     {{$famiglia->capofamiglia()->first()->nominativo}}</a>
                           @endif</div>
                      <div class="font-weight-bold">
                          @if ($famiglia->moglie->isNotEmpty())  
                          <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$famiglia->moglie->first()->id])}}"> {{$famiglia->moglie->first()->nominativo}}</a>
                         @endif
                      </div>
                      <ul>
                        @foreach($famiglia->figli as $figlio)
                        <li>{{Carbon::parse($figlio->data_nascita)->year}}  
                          <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$figlio->id])}}">  {{$figlio->nominativo}}</a>
                       </li>
                        @endforeach
                      </ul>
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
