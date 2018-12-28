@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Persona'])
<div class="row">
  <div class="col-md-6"> <!--  start col dati anagrafici -->
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
            <label for="staticEmail" class="col-sm-6 col-form-label">Nominativo:</label>
            <div class="col-sm-6">
              <p>{{$persona->nominativo}}</p>
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Nome Cognome</label>
            <div class="col-sm-6">
                  <p>{{$persona->datipersonali->nome}} {{$persona->datipersonali->cognome}}</p>
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
        </div>
      </div>
    </div>
  </div> <!--  end col dati anagrafici -->

  <div class="col-md-6"> <!--  start col dati famiglia -->
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
              @if($persona->statoAttuale()->first() != null)
              <option>  {{$persona->statoAttuale()->first()->nome}}</option>
              @endif
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Famiglia</label>
            <div class="col-sm-6">
              @if($persona->statoAttuale()->first() != null)
              <option>  {{$persona->famigliaAttuale()->first()->nome_famiglia}}</option>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> <!--  end col dati famiglia-->

</div> <!--  end first row-->

<div class="row">
  <div class="col-md-6"> <!--  start col dati nmadelfia -->
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
              @if($persona->statoAttuale()->first() != null)
              <option>  {{$persona->statoAttuale()->first()->nome}}</option>
              @endif
            </div>
          </div>
          <div class="row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Gruppo familiare</label>
            <div class="col-sm-6">
              @if($persona->statoAttuale()->first() != null)
              <option>  {{$persona->famigliaAttuale()->first()->nome_famiglia}}</option>
              @endif
            </div>
          </div>
          <div class="row">
              <label for="inputPassword" class="col-sm-6 col-form-label">Azienda:</label>
              <div class="col-sm-6">
                @if($persona->statoAttuale()->first() != null)
                <option>  {{$persona->famigliaAttuale()->first()->nome_famiglia}}</option>
                @endif
              </div>
           </div>
        </div>
      </div>
    </div>
  </div> <!--  end col dati nomadelifia-->
</div> 


 <div class="form-check">
        <input type="checkbox" class="form-check-input" id="exampleCheck1">
        <label class="form-check-label" for="exampleCheck1">Presente in Nomadelfia</label>
      </div>

<!--  
<div class="row">
  <div class="col-md-6">
    <form>
      <div class="form-group">
        <label>Posizione</label>
        <select class="form-control">
          @foreach (App\Nomadelfia\Models\Posizione::all() as $posizione)
              <option >{{ $posizione->nome }} ({{ $posizione->abbreviato }})</option>
          @endforeach
        </select>
      </div>
      <label>Stato</label>
        <select class="form-control">
          @foreach (App\Nomadelfia\Models\Stato::all() as $stato)
            <option >{{ $stato->nome }} ({{ $stato->stato }})</option>
         @endforeach
        </select>
      <div class="form-group">
        <label >Gruppo Familiare</label>
          <select class="form-control">
          @foreach (App\Nomadelfia\Models\GruppoFamiliare::all() as $gruppo)
              <option >{{ $gruppo->nome }}</option>
          @endforeach
         </select>
      </div>
      <div class="form-group">
        <label >Azienda</label>
          <select class="form-control">
          @foreach (App\Nomadelfia\Models\Azienda::all() as $azienda)
              <option >{{ $azienda->nome_azienda }}</option>
          @endforeach
         </select>
      </div>
     
      </form>
    </div>
  </div>
</div>
end col dati nomadelifia-->
@endsection
