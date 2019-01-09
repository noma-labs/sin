@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => $persona->nome." ". $persona->cognome])

<div class="container">

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <label class="col-md-2">Stato:</label> 
          <div class="col-md-6">
            @if($persona->categoria != null)
                <span class="text-bold"> {{$persona->categoria->nome}} </span>
              @else
              <span class="text-danger">Nessuno stato</span>
              @endif
          </div>
          <div class="col-md-4">
            <my-modal modal-title="Modifica stato persona" button-title="Modifica">
              <template slot="modal-body-slot">
              <form class="form" method="POST"  id="formStato" action="{{ route('nomadelfia.persone.categoria.modifica', ['idPersona' =>$persona->id]) }}" >      
                  {{ csrf_field() }}
                  @foreach (App\Nomadelfia\Models\Categoria::all() as $cat)
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="categoria" id="categoria{{$cat->id}}" value="{{$cat->id}}" {{ $persona->categoria_id == $cat->id ? 'checked' : '' }}>
                      <label class="form-check-label" for="categoria{{$cat->id}}">
                      <span class="font-weight-bold">{{ $cat->nome}}</span> (<span class="font-weight-light">{{ $cat->descrizione}}<span>)
                      </label>
                    </div>
                  @endforeach
                </form>
              </template> 
              <template slot="modal-button">
                    <button class="btn btn-danger" form="formStato">Salva</button>
              </template>
            </my-modal>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</div>

<div class="row my-3">
<div class="col-md-4 mb-2"> <!--  start col dati anagrafici -->
    <div class="card">
      <div class="card-header" id="headingOne">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            Dati anagrafici
          </button>
        </h5>
      </div>
      <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
        <div class="card-body">

        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4">Nome:</label>
              <div class="col-sm-8">
                <span>{{$persona->nome}} </span>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4">Cognome:</label>
              <div class="col-sm-8">
                    <span>{{$persona->cognome}}</span>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4">Data Nascita:</label>
              <div class="col-sm-8">
                    <span>{{$persona->data_nascita}}</span>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4">Luogo Nascita:</label>
              <div class="col-sm-8">
                  <span>{{$persona->provincia_nascita}}</span>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4">Sesso:</label>
              <div class="col-sm-8">
                <span>{{$persona->sesso}}</span>
              </div>
            </div>
          </li>
        </ul>
          <!-- <div class="row"> -->
            <a class="btn btn-primary my-2"  href="{{route('nomadelfia.persone.anagrafica.modifica', $persona->id)}}"  role="button">Modifica</a>
          <!-- </div> -->
        </div>
      </div>
    </div>
  </div> <!--  end col dati anagrafici -->


  <div class="col-md-5"> <!--  start col dati nomadelfia -->
    <div class="card mb-2">
      <div class="card-header" id="headingZero">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapsezero" aria-expanded="true" aria-controls="collapsezero">
            Dati Nomadelfia
          </button>
        </h5>
      </div>
      <div id="collapsezero" class="collapse show" aria-labelledby="headingZero" data-parent="#accordion">
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <div class="row">
                <!-- <  p for="staticEmail" class="col-sm-6 col-form-label">Nominativo Attuale:</span> -->
                <label class="col-sm-4">Nominativo: </label>
                <div class="col-sm-4">
                  {{$persona->nominativo}}
                </div>
                <div class="col-sm-2">
                <a class="btn btn-primary"  href="{{route('nomadelfia.persone.nominativo.modifica', $persona->id)}}"  role="button">Modifica</a>
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4">Posizione: </label>
                <div class="col-sm-8">
                @if($persona->posizioneAttuale() != null)
                      {{$persona->posizioneAttuale()->nome}}
                    @else
                    <span class="text-danger">Nessuna posizione</span>
                    @endif
                </div>
            </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4">Gruppo familiare: </label>
                <div class="col-sm-8">
                  @if($persona->gruppofamiliareAttuale()  != null)
                    <a href="{{route('nomadelfia.gruppifamiliari.modifica', [$persona->gruppofamiliareAttuale()->id])}}">{{ $persona->gruppofamiliareAttuale()->nome }} </a> </option>
                  @else
                    <span class="text-danger">Nessun gruppo</span>
                  @endif
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4">Azienda/e:</label>
                <div class="col-sm-8">
                  @forelse ($persona->aziendeAttuali()->get() as $azienda)
                      <span> <a href="{{route('nomadelfia.aziende.edit', [$azienda->id])}}">{{ $azienda->nome_azienda }} </a> ({{ $azienda->pivot->mansione }})</span>
                  @empty
                      <span class="text-danger">Nessuna azienda</span>
                  @endforelse
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div> 
    </div>  <!--  end card -->
  </div> <!--  end col dati nomadelfia -->

 
   <div class="col-md-3"> <!--  start col dati famiglia -->
    <div class="card">
      <div class="card-header" id="headingTwo">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            Dati Famiglia
          </button>
        </h5>
      </div>
      <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
        <div class="card-body">
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <div class="row">
                <label class="col-sm-4">Stato:</label>
                <div class="col-sm-8">
                  @if ($persona->statoAttuale() != null)
                      <span>{{$persona->statoAttuale()->nome}}</span>
                  @else
                      <span class="text-danger">Nessuno stato</span>
                  @endif
                </div>

            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4">Famiglia:</label>
              <div class="col-sm-8">
                @if($persona->famigliaAttuale() != null)
                  <span> {{$persona->famigliaAttuale()->nome_famiglia}} ({{$persona->famigliaAttuale()->pivot->posizione_famiglia}})</span>
                @else
                  <span class="text-danger">Nessuna famiglia</span>
                @endif
              </div>
            </div>
          </li>
        </ul>
        </div>
      </div>
    </div>
  </div> <!--  end col dati famiglia-->
 
</div> <!--  end first row-->

@endsection
