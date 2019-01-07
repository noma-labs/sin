@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => $persona->nome." ". $persona->cognome])

 
 <form class="form" method="POST" action="{{ route('nomadelfia.persone.categoria.modifica', ['idPersona' =>$persona->id]) }}" >      
    {{ csrf_field() }}
<div class="row justify-content-md-center">
  <label class="col-md-2 col-form-label offset-md-2">Stato persona</label>
    <div class="col-md-4">
      <div class="form-group">
          <select class="form-control"  name="categoria">
          <option value='{{ $persona->categoria_id }}' selected>{{ $persona->categoria->nome }}</option>
          @foreach (App\Nomadelfia\Models\Categoria::all() as $cat)
                @if($persona->categoria_id != $cat->id)
                  @if(old('categoria') == $cat->id)
                <option value="{{$cat->id}}" selected> {{ $cat->nome}}</option>
                @else
                <option value="{{$cat->id}}" > {{ $cat->nome}} {{ $cat->descrizione}}</option>
                @endif

                @endif
          @endforeach
        </select>
      </div>
    </div>  
    <div class="col-md-4">
    <button class="btn btn-success" type="submit">Salva</button>
  </div>
</div>
</form>


<div class="row my-3">
  <div class="col-md-5"> <!--  start col dati nomadelfia -->
    <div class="card">
      <div class="card-header" id="headingZero">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapsezero" aria-expanded="true" aria-controls="collapsezero">
            Dati Nomadelfia
          </button>
        </h5>
      </div>
      <div id="collapsezero" class="collapse show" aria-labelledby="headingZero" data-parent="#accordion">
        <div class="card-body">
          <div class="row ">
            <label for="staticEmail" class="col-sm-6 col-form-label">Nominativo Attuale:</label>
            <div class="col-sm-4">
              <p>{{$persona->nominativo}}</p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Stato attuale:</label>
            <div class="col-sm-6">
              @if($persona->categoria->nome != null)
              <option>  {{$persona->categoria->nome}}</option>
              @else
                <p class="text-danger">Nessuna stato attuale</p>
              @endif
            </div>
          </div>
          <div class="row">
            <label for="staticEmail" class="col-sm-6 col-form-label">Posizione in Nomadelfia:</label>
            <div class="col-sm-6">
              @if($persona->posizioneAttuale() != null)
              <option>  {{$persona->posizioneAttuale()->nome}}</option>
              @else
                <p class="text-danger">Nessuna posizione</p>
              @endif
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Gruppo familiare:</label>
            <div class="col-sm-6">
              @if($persona->gruppofamiliareAttuale()  != null)
                <a href="{{route('nomadelfia.gruppifamiliari.modifica', [$persona->gruppofamiliareAttuale()->id])}}">{{ $persona->gruppofamiliareAttuale()->nome }} </a> </option>
              @else
                <p class="text-danger">Nessun gruppo</p>
              @endif
            </div>
          </div>
          <div class="row">
              <label for="inputPassword" class="col-sm-6 col-form-label">Azienda/e:</label>
              <div class="col-sm-6">
                @forelse ($persona->aziendeAttuali()->get() as $azienda)
                    <li> <a href="{{route('nomadelfia.aziende.edit', [$azienda->id])}}">{{ $azienda->nome_azienda }} </a> ({{ $azienda->pivot->mansione }})</li>
                @empty
                    <p class="text-danger">Nessuna azienda</p>
                @endforelse
              </div>
           </div>
         
          <div class="row">
            <a class="btn btn-primary"  href="{{route('nomadelfia.persone.nominativo.modifica', $persona->id)}}"  role="button">Modifica</a>
          </div>
        </div>
      </div>
    </div>
  </div> <!--  end col dati generali -->

  <div class="col-md-4"> <!--  start col dati anagrafici -->
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
          <div class="row">
            <label for="staticEmail" class="col-sm-6 col-form-label">Nome:</label>
            <div class="col-sm-6">
              <p>{{$persona->nome}} </p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Cognome:</label>
            <div class="col-sm-6">
                  <p>{{$persona->cognome}}</p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Data Nascita:</label>
            <div class="col-sm-6">
                  <p>{{$persona->data_nascita}}</p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Luogo Nascita;</label>
            <div class="col-sm-6">
                  <p>{{$persona->provincia_nascita}}</p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Sesso:</label>
            <div class="col-sm-6">
            
                  <p>{{$persona->sesso}}</p>
            </div>
          </div>
          <div class="row">
            <a class="btn btn-primary"  href="{{route('nomadelfia.persone.anagrafica.modifica', $persona->id)}}"  role="button">modifica</a>
          </div>
        </div>
      </div>
    </div>
  </div> <!--  end col dati anagrafici -->

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
          <div class="row">
            <label for="staticEmail" class="col-sm-6 col-form-label">Stato:</label>
            <div class="col-sm-6">
              @if($persona->statoAttuale() != null)
              <option>  {{$persona->statoAttuale()->nome}}</option>
              @endif
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Famiglia</label>
            <div class="col-sm-6">
              @if($persona->famigliaAttuale() != null)
              <option>  {{$persona->famigliaAttuale()->nome_famiglia}}</option>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> <!--  end col dati famiglia-->
 
</div> <!--  end first row-->

@endsection
