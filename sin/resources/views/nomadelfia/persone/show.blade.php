@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Persona'])
<div class="row my-3">
  <div class="col-md-4"> <!--  start col dati generali -->
    <div class="card">
      <div class="card-header" id="headingZero">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapsezero" aria-expanded="true" aria-controls="collapsezero">
            Dati Generali
          </button>
        </h5>
      </div>
      <div id="collapsezero" class="collapse show" aria-labelledby="headingZero" data-parent="#accordion">
        <div class="card-body">
          <div class="row">
            <label for="staticEmail" class="col-sm-5 col-form-label">Nominativo:</label>
            <div class="col-sm-7">
              <p>{{$persona->nominativo}}</p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-5 col-form-label">Stato attuale:</label>
            <div class="col-sm-7">
                  <p>{{$persona->categoria->nome}}</p>
            </div>
          </div>
         
          <div class="row">
            <a class="btn btn-primary"  href="{{route('nomadelfia.persone.anagrafica.modifica', $persona->id)}}"  role="button">modifica</a>
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
              <p>{{$persona->datipersonali->nome}} </p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Cognome</label>
            <div class="col-sm-6">
                  <p>{{$persona->datipersonali->cognome}}</p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Data Nascita</label>
            <div class="col-sm-6">
                  <p>{{$persona->datipersonali->data_nascita}}</p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Luogo Nascita</label>
            <div class="col-sm-6">
                  <p>{{$persona->datipersonali->provincia_nascita}}</p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Sesso:</label>
            <div class="col-sm-6">
            
                  <p>{{$persona->datipersonali->sesso}}</p>
            </div>
          </div>
          <div class="row">
            <a class="btn btn-primary"  href="{{route('nomadelfia.persone.anagrafica.modifica', $persona->id)}}"  role="button">modifica</a>
          </div>
        </div>
      </div>
    </div>
  </div> <!--  end col dati anagrafici -->

   <div class="col-md-4"> <!--  start col dati famiglia -->
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

<div class="row">
  <div class="col-md-8"> <!--  start col dati nmadelfia -->
    <div class="card">
      <div class="card-header" id="headingThree">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
            Dati Nomadelfia
          </button>
        </h5>
      </div>
      <div id="collapseThree" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion">
        <div class="card-body">
          <div class="row">
            <label for="staticEmail" class="col-sm-6 col-form-label">Posizione:</label>
            <div class="col-sm-6">
              @if($persona->posizioneAttuale() != null)
              <option>  {{$persona->posizioneAttuale()->nome}}</option>
              @endif
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Gruppo familiare</label>
            <div class="col-sm-6">
              @if($persona->gruppofamiliareAttuale()  != null)
              <option>  {{$persona->gruppofamiliareAttuale()->nome}}</option>
              @endif
            </div>
          </div>
          <div class="row">
              <label for="inputPassword" class="col-sm-6 col-form-label">Azienda:</label>
              <div class="col-sm-6">
                @forelse ($persona->aziendeAttuali()->get() as $azienda)
                    <li>{{ $azienda->nome_azienda }}  ({{ $azienda->pivot->mansione }})</li>
                @empty
                    <p>Nessuna azienda</p>
                @endforelse
              </div>
           </div>
        </div>
      </div>
    </div>
  </div> <!--  end col dati nomadelifia-->
</div>   <!--  end second row-->

@endsection
