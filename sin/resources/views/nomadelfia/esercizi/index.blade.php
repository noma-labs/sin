@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Esercizi Spirituali'])

@foreach(collect($esercizi)->chunk(3) as $chunk)
 <div class="row">
    @foreach ($chunk as $esercizio)
      <div class="col-md-4 my-1">
          <div id="accordion">
            <div class="card">
              <div class="card-header" id="heading{{$esercizio->id}}">
                <h5 class="mb-0">
                  <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$esercizio->id}}" aria-expanded="false" aria-controls="collapse{{$esercizio->id}}">
                      {{$esercizio->turno}}  
                  <span class="badge badge-primary badge-pill"> {{$esercizio->personeOk()->total }}</span> 
                  </button>
                </h5>
              </div>
              <div id="collapse{{$esercizio->id}}" class="collapse" aria-labelledby="heading{{$esercizio->id}}" data-parent="#accordion">
                <div class="card-body">
                  <p> Responsabile: {{ $esercizio->responsabile->nominativo}}</p>
                  <a class="btn btn-primary" href="{{ route('nomadelfia.esercizi.dettaglio', $esercizio->id)}}">Modifica</a>
                </div>    
              </div>
            </div>
          </div>
      </div>      
     @endforeach
 </div> 
@endforeach

@endsection