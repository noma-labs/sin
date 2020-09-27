@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Gruppi Familiari'])

@foreach($gruppifamiliari->chunk(3) as $chunk)
 <div class="row">
    @foreach ($chunk as $gruppo)
      <div class="col-md-4 my-1">
          <div id="accordion">
            <div class="card">
              <div class="card-header" id="heading{{$gruppo->id}}">
                <h5 class="mb-0">
                  <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$gruppo->id}}" aria-expanded="false" aria-controls="collapse{{$gruppo->id}}">
                      {{$gruppo->nome}} 
                  <span class="badge badge-primary badge-pill">
                  {{$gruppo->personeAttuale->count()}}
                  </span> 
                  </button>
                </h5>
              </div>
              <div id="collapse{{$gruppo->id}}" class="collapse" aria-labelledby="heading{{$gruppo->id}}" data-parent="#accordion">
                <div class="card-body">
                    @if ($gruppo->capogruppoAttuale())
                    <p class="font-weight-bold"> Capogruppo: 
                        <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$gruppo->capogruppoAttuale()->id])}}">  
                      {{$gruppo->capogruppoAttuale()->nominativo}}</a>
                    </p> 
                    @else
                      <p class="text-danger">Senza capogruppo</p> 
                    @endif
                  <ul>
                    @foreach(App\Nomadelfia\Models\GruppoFamiliare::CountPosizioniFamiglia($gruppo->id)->get() as $posizione)
                      <li> {{$posizione->posizione_famiglia}}  <span class="badge badge-info"> {{$posizione->total}}</span></li>
                    @endforeach
                  </ul>
                    <a class="btn btn-primary" href="{{ route('nomadelfia.gruppifamiliari.dettaglio', $gruppo->id)}}">Modifica</a>
                </div>    
              </div>
            </div>
          </div>
      </div>      
     @endforeach
 </div> 
@endforeach

@endsection
