@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Gruppi Familiari'])

<div class="container">
 <div class="row">
    @foreach($gruppifamiliari->chunk(6) as $chunk)
      <div class="col-md-6">
          @foreach ($chunk as $gruppo)
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
                        <p class="font-weight-bold"> Capogruppo: {{$gruppo->capogruppiAttuali->first()->nominativo}}</p> 
                        @else
                        <p class="text-danger">Senza capogruppo</p> 
                      @endif
                      @foreach($gruppo->famiglie as $famiglia)
                          @if ($famiglia->single->isNotEmpty())
                          <div class="font-weight-bold"> {{$famiglia->single->first()->nominativo}}</div>
                          @else
                          <div class="font-weight-bold mt-3">@if ($famiglia->capofamiglia->isNotEmpty())  {{$famiglia->capofamiglia()->first()->nominativo}} @endif</div>
                          <div class="font-weight-bold">@if ($famiglia->moglie->isNotEmpty())  {{$famiglia->moglie->first()->nominativo}} @endif</div>
                          <ul>
                            @foreach($famiglia->figli as $figlio)
                            <li>{{Carbon::parse($figlio->data_nascita_persona)->year}} {{$figlio->nominativo}}</li>
                            @endforeach
                          </ul>
                          @endif
                        @endforeach
                          <div class="row">
                          <div class="col-md-4 offset-md-6">
                            <a class="btn btn-danger btn-block" type="button" href="{{ route('nomadelfia.gruppifamiliari.modifica', $gruppo->id)}}">Modifica</a>
                          </div>
                      </div>
                    </div>    
                  </div>
                </div>
             </div>
             @endforeach
          </div>
        @endforeach
     </div> 
</div>

@endsection
