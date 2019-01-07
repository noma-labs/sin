@extends('nomadelfia.index')


@section('archivio')
@include('partials.header', ['title' => 'Gestione Persone'])

@foreach (App\Nomadelfia\Models\Posizione::orderby('ordinamento')->get()->chunk(3) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $posizione)
            <div class="col-md-4">
                <div id="accordion">
                  <div class="card">
                  <div class="card-header" id="head{{$posizione->id}}">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#{{$posizione->id}}" aria-expanded="true" aria-controls="{{$posizione->id}}">
                      {{ $posizione->nome }} {{$posizione->persone()->presente()->count()}}
                      </button>
                    </h5>
                  </div>
                  <div id="{{$posizione->id}}" class="collapse" aria-labelledby="head{{$posizione->id}}" data-parent="#accordion">
                    <div class="card-body">
                    <div class="row">
                      <div class="col-md-6"> 
                        <h5>Uomini {{$posizione->persone()->presente()->uomini()->count()}}</h5>
                          
                          @foreach($posizione->persone()->uomini()->get() as $uomo)
                            <div>
                              <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$uomo->id])}}">  {{$uomo->nominativo}}</a>
                            </div>
                          @endforeach
                      </div>
                      <div class="col-md-6"> 
                        <h5>Donne {{$posizione->persone()->presente()->donne()->count()}}</h5>
                          @foreach($posizione->persone()->donne()->get() as $donna)
                            <div>
                            <a href="{{route('nomadelifa.persone.dettaglio',['idPersona'=>$donna->id])}}">  {{$donna->nominativo}}</a>                 
                            </div>
                        @endforeach
                      </div>
                    </div>
                    </div>
                  </div>
                </div> <!-- end nomadelfi effettivi card -->
              </div> <!-- end accordion -->
            </div>
        @endforeach
    </div>
@endforeach

@endsection

