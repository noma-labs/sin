@extends('nomadelfia.index')


@section('archivio')
@include('partials.header', ['title' => 'Gestione Persone'])

<h1 class="display-5">Categoria</h1>
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
                      <span class="badge badge-primary badge-pill">{{$categoria->personeAttuale()->attivo()->count()}}</span> 
                      </button>
                    </h5>
                  </div>
                  <div id="{{$categoria->nome}}" class="collapse" aria-labelledby="head{{$categoria->nome}}" data-parent="#accordion">
                    <div class="card-body">
                    <div class="row">
                      <div class="col-md-6"> 
                        <h5>Uomini 
                        <span class="badge badge-primary badge-pill">{{$categoria->personeAttuale()->attivo()->uomini()->count()}}</span> 
                        </h5>
                          @foreach($categoria->personeAttuale()->attivo()->uomini()->get() as $uomo)
                            <div>@include("nomadelfia.templates.persona", ['persona' => $uomo])</div>
                          @endforeach
                      </div>
                      <div class="col-md-6"> 
                        <h5>Donne
                        <span class="badge badge-primary badge-pill"> {{$categoria->personeAttuale()->attivo()->donne()->count()}}</span> 
                        </h5>
                          @foreach($categoria->personeAttuale()->attivo()->donne()->get() as $donna)
                            <div>@include("nomadelfia.templates.persona", ['persona' => $donna])</div>
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

<h1 class="display-5">Stato Familiare</h1>
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
                      <span class="badge badge-primary badge-pill">{{$stato->personeAttuale()->attivo()->count()}}</span> 
                      </button>
                    </h5>
                  </div>
                  <div id="{{$stato->nome}}" class="collapse" aria-labelledby="stato{{$stato->id}}" data-parent="#accordion">
                    <div class="card-body">
                    <div class="row">
                      <div class="col-md-6"> 
                        <h5>Uomini 
                        <span class="badge badge-primary badge-pill">{{$stato->personeAttuale()->attivo()->uomini()->count()}}</span> 
                        </h5>
                          @foreach($stato->personeAttuale()->attivo()->uomini()->get() as $uomo)
                            <div>@include("nomadelfia.templates.persona", ['persona' => $uomo])</div>
                          @endforeach
                      </div>
                      <div class="col-md-6"> 
                        <h5>Donne
                        <span class="badge badge-primary badge-pill"> {{$stato->personeAttuale()->attivo()->donne()->count()}}</span> 
                        </h5>
                          @foreach($stato->personeAttuale()->attivo()->donne()->get() as $donna)
                            <div>@include("nomadelfia.templates.persona", ['persona' => $donna])</div>
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


<h1 class="display-5">Posizioni</h1>
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
                      <span class="badge badge-primary badge-pill">{{$posizione->personeAttuale()->attivo()->count()}}</span> 
                      </button>
                    </h5>
                  </div>
                  <div id="{{$posizione->id}}" class="collapse" aria-labelledby="head{{$posizione->id}}" data-parent="#accordion">
                    <div class="card-body">
                    <div class="row">
                      <div class="col-md-6"> 
                        <h5>Uomini 
                        <span class="badge badge-primary badge-pill">{{$posizione->personeAttuale()->attivo()->uomini()->count()}}</span> 
                        </h5>
                          @foreach($posizione->personeAttuale()->attivo()->uomini()->get() as $uomo)
                          <div>@include("nomadelfia.templates.persona", ['persona' => $uomo])</div>
                          @endforeach
                      </div>
                      <div class="col-md-6"> 
                        <h5>Donne
                        <span class="badge badge-primary badge-pill"> {{$posizione->personeAttuale()->attivo()->donne()->count()}}</span> 
                        </h5>
                          @foreach($posizione->personeAttuale()->attivo()->donne()->get() as $donna)
                           <div>@include("nomadelfia.templates.persona", ['persona' => $donna])</div>
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

<!-- <div class="col-md-3">
    <div id="accordion">
      <div class="card">
      <div class="card-header" id="statoSenzafmiaglia">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#8888" aria-expanded="true" aria-controls="8888">
          <span class="text-lowercase">Persone senza famiglia </span>
          </button>
        </h5>
      </div>
      <div id="8888" class="collapse" aria-labelledby="statoSenzafmiaglia" data-parent="#accordion">
        <div class="card-body">
        <div class="row">
          <div class="col-md-6"> 
            Prsone senza famiglia
            @foreach (App\Nomadelfia\Models\Persona::doesnthave("famiglie")->get() as $persona)
            <div>
              @include('nomadelfia.templates.persona',['persona'=> $persona])
            </div>
            @endforeach
          </div>
        </div>
        </div>
      </div>
    </div> <! -- end nomadelfi effettivi card -- >
  </div> <! -- end accordion -- >
</div> -->
@endsection

