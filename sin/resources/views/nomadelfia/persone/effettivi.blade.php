@extends('nomadelfia.index')


@section('archivio')
@include('partials.header', ['title' => 'Popolazione Nomadelfia'])

<div class="row my-2">
    <div class="col-md-4">
        <div id="accordion">
          <div class="card">
          <div class="card-header" id="effettivi">
            <h5 class="mb-0">
              <button class="btn btn-link" data-toggle="collapse" data-target="#effettivi" aria-expanded="true" aria-controls="effettivi">
              Effettivi 
              <span class="badge badge-primary badge-pill"></span> 
              </button>
            </h5>
          </div>
          <div id="effettivi" class="collapse" aria-labelledby="effettivi" data-parent="#accordion">
            <div class="card-body">
            <div class="row">
              <div class="col-md-6"> 
                <h5>Uomini<span class="badge badge-primary badge-pill"> COUNT UOMINI</span> </h5>
                  @foreach($effettivi as $effettivo)
                    @if($effettivo->sesso == "M")
                     <div>@include("nomadelfia.templates.persona", ['persona' => $effettivo]) {{$effettivo->data_inizio}}</div>
                    @endif
                  @endforeach
              </div>
              <div class="col-md-6"> 
                <h5>Donne <span class="badge badge-primary badge-pill"> COUNT DONNE</span> </h5>
                @foreach($effettivi as $effettivo)
                  @if($effettivo->sesso == "F")
                  <div>@include("nomadelfia.templates.persona", ['persona' => $effettivo]) {{$effettivo->data_inizio}}</div>
                  @endif
                @endforeach
              </div>
            </div>
            </div>
          </div>
        </div> <!-- end nomadelfi effettivi card -->
      </div> <!-- end accordion -->
    </div>

    <div class="col-md-4">
        <div id="accordion">
          <div class="card">
          <div class="card-header" id="postulanti">
            <h5 class="mb-0">
              <button class="btn btn-link" data-toggle="collapse" data-target="#postulanti" aria-expanded="true" aria-controls="postulanti">
              Postulanti 
              <span class="badge badge-primary badge-pill">{{count($postulanti)}}</span> 
              </button>
            </h5>
          </div>
          <div id="postulanti" class="collapse" aria-labelledby="postulanti" data-parent="#accordion">
            <div class="card-body">
            <div class="row">
              <div class="col-md-6"> 
                <h5>Uomini<span class="badge badge-primary badge-pill"> COUNT UOMINI</span> </h5>
                  
              </div>
              <div class="col-md-6"> 
                <h5>Donne <span class="badge badge-primary badge-pill"> COUNT DONNE</span> </h5>
                  
              </div>
            </div>
            </div>
          </div>
        </div> <!-- end nomadelfi effettivi card -->
      </div> <!-- end accordion -->
    </div>

</div>

<h1 class="display-5">Categoria</h1>
@foreach (App\Nomadelfia\Models\Categoria::all()->chunk(3) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $categoria)
            <div class="col-md-4">
                <div id="accordion">
                  <div class="card">
                  <div class="card-header" id="heading{{$categoria->id}}">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$categoria->id}}" aria-expanded="true" aria-controls="collapse{{$categoria->id}}">
                      {{ $categoria->nome }} 
                      <span class="badge badge-primary badge-pill">COUNT TOTALE</span> 
                      </button>
                    </h5>
                  </div>
                  <div id="collapse{{$categoria->id}}" class="collapse" aria-labelledby="heading{{$categoria->id}}" data-parent="#accordion">
                    <div class="card-body">
                    <div class="row">
                      <div class="col-md-6"> 
                        <h5>Uomini   <span class="badge badge-primary badge-pill"> COUNT UOMINI</span> </h5>
                          @foreach($categoria->personeAttuale() as $uomo)
                            <div>@include("nomadelfia.templates.persona", ['persona' => $uomo])</div>
                          @endforeach
                      </div>
                      <div class="col-md-6"> 
                        <h5>Donne <span class="badge badge-primary badge-pill"> COUNT DONNE</span> </h5>
                         
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
@foreach (App\Nomadelfia\Models\Posizione::all()->chunk(3) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $posizione)
            <div class="col-md-4">
                <div id="accordion">
                  <div class="card">
                  <div class="card-header" id="head{{$posizione->id}}">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#posizione{{$posizione->id}}" aria-expanded="false" aria-controls="posizione{{$posizione->id}}">
                      <span class="text-lowercase">{{ $posizione->nome }}</span>
                      <span class="badge badge-primary badge-pill">{{$posizione->personeAttuale()->attivo()->count()}}</span> 
                      </button>
                    </h5>
                  </div>
                  <div id="posizione{{$posizione->id}}" class="collapse" aria-labelledby="head{{$posizione->id}}" data-parent="#accordion">
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

<h1 class="display-5">Stato Familiare</h1>
@foreach (App\Nomadelfia\Models\Stato::orderby("nome")->get()->chunk(3) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $stato)
            <div class="col-md-4">
                <div id="accordion">
                  <div class="card">
                  <div class="card-header" id="heading{{$stato->nome}}">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#familiare{{$stato->id}}" aria-expanded="false" aria-controls="familiare{{$stato->id}}">
                      <span class="text-lowercase">{{ $stato->nome }}</span>
                      <span class="badge badge-primary badge-pill">{{$stato->personeAttuale()->attivo()->count()}}</span> 
                      </button>
                    </h5>
                  </div>
                  <div id="familiare{{$stato->id}}" class="collapse" aria-labelledby="heading{{$stato->nome}}" data-parent="#accordion">
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

@endsection
