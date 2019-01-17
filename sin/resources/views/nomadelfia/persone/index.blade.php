@extends('nomadelfia.index')


@section('archivio')
@include('partials.header', ['title' => 'Gestione Persone'])

<!-- <div class="text-center"> -->
  <h1 class="display-5">Categoria</h1>
<!-- </div> -->
@foreach (App\Nomadelfia\Models\Categoria::all()->chunk(4) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $categoria)
            <div class="col-md-3">
                <div id="accordion">
                  <div class="card">
                  <div class="card-header" id="head{{$categoria->nome}}">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#{{$categoria->nome}}" aria-expanded="true" aria-controls="{{$categoria->id}}">
                      {{ $categoria->nome }} 
                      <span class="badge badge-primary badge-pill">{{$categoria->persone()->presente()->count()}}</span> 
                      </button>
                    </h5>
                  </div>
                  <div id="{{$categoria->nome}}" class="collapse" aria-labelledby="head{{$categoria->nome}}" data-parent="#accordion">
                    <div class="card-body">
                    <div class="row">
                      <div class="col-md-6"> 
                        <h5>Uomini 
                        <span class="badge badge-primary badge-pill">{{$categoria->persone()->presente()->uomini()->count()}}</span> 
                        </h5>
                          @foreach($categoria->persone()->presente()->uomini()->get() as $uomo)
                            <div>
                              <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$uomo->id])}}">  {{$uomo->nominativo}}</a>
                            </div>
                          @endforeach
                      </div>
                      <div class="col-md-6"> 
                        <h5>Donne
                        <span class="badge badge-primary badge-pill"> {{$categoria->persone()->presente()->donne()->count()}}</span> 
                        </h5>
                          @foreach($categoria->persone()->presente()->donne()->get() as $donna)
                            <div>
                            <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$donna->id])}}">  {{$donna->nominativo}}</a>                 
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

<!-- <div class="text-center"> -->
<h1 class="display-5">Posizioni</h1>
<!-- </div> -->
@foreach (App\Nomadelfia\Models\Posizione::all()->chunk(4) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $posizione)
            <div class="col-md-3">
                <div id="accordion">
                  <div class="card">
                  <div class="card-header" id="head{{$posizione->id}}">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#{{$posizione->id}}" aria-expanded="true" aria-controls="{{$posizione->id}}">
                      <span class="text-lowercase">{{ $posizione->nome }}</span>
                      <span class="badge badge-primary badge-pill">{{$posizione->persone()->presente()->count()}}</span> 
                      </button>
                    </h5>
                  </div>
                  <div id="{{$posizione->id}}" class="collapse" aria-labelledby="head{{$posizione->id}}" data-parent="#accordion">
                    <div class="card-body">
                    <div class="row">
                      <div class="col-md-6"> 
                        <h5>Uomini 
                        <span class="badge badge-primary badge-pill">{{$posizione->persone()->presente()->uomini()->count()}}</span> 
                        </h5>
                          @foreach($posizione->persone()->presente()->uomini()->get() as $uomo)
                            <div>
                              <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$uomo->id])}}">  {{$uomo->nominativo}}</a>
                            </div>
                          @endforeach
                      </div>
                      <div class="col-md-6"> 
                        <h5>Donne
                        <span class="badge badge-primary badge-pill"> {{$posizione->persone()->presente()->donne()->count()}}</span> 
                        </h5>
                          @foreach($posizione->persone()->presente()->donne()->get() as $donna)
                            <div>
                            <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$donna->id])}}">  {{$donna->nominativo}}</a>                 
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

<!-- <div class="text-center"> -->
  <h1 class="display-5">Stato</h1>
<!-- </div> -->
@foreach (App\Nomadelfia\Models\Stato::orderby("nome")->get()->chunk(4) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $stato)
            <div class="col-md-3">
                <div id="accordion">
                  <div class="card">
                  <div class="card-header" id="stato{{$stato->id}}">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#{{$stato->nome}}" aria-expanded="true" aria-controls="{{$stato->nome}}">
                      <span class="text-lowercase">{{ $stato->nome }}</span>
                      <span class="badge badge-primary badge-pill">{{$stato->persone()->presente()->count()}}</span> 
                      </button>
                    </h5>
                  </div>
                  <div id="{{$stato->nome}}" class="collapse" aria-labelledby="stato{{$stato->id}}" data-parent="#accordion">
                    <div class="card-body">
                    <div class="row">
                      <div class="col-md-6"> 
                        <h5>Uomini 
                        <span class="badge badge-primary badge-pill">{{$stato->persone()->presente()->uomini()->count()}}</span> 
                        </h5>
                          @foreach($stato->persone()->presente()->uomini()->get() as $uomo)
                            <div>
                              <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$uomo->id])}}">  {{$uomo->nominativo}}</a>
                            </div>
                          @endforeach
                      </div>
                      <div class="col-md-6"> 
                        <h5>Donne
                        <span class="badge badge-primary badge-pill"> {{$stato->persone()->presente()->donne()->count()}}</span> 
                        </h5>
                          @foreach($stato->persone()->presente()->donne()->get() as $donna)
                            <div>
                            <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$donna->id])}}">  {{$donna->nominativo}}</a>                 
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

