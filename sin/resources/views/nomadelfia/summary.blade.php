@extends('nomadelfia.index')

@section('title', 'Gestione Nomadelfia')


@section('archivio')

 <div class="card-deck">

    <div class="card ">
      <div class="card-header">
        Gestione Popolazione
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush ">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{route('nomadelfia.popolazione')}}"> Popolazione Nomadelfia</a>
               
              <span class="badge badge-primary badge-pill"> {{$totale}}</span> </li>
            <li class="list-group-item  d-flex justify-content-between align-items-center">
                <a href="{{route('nomadelfia.popolazione.posizione.effettivi')}}">  Effettivi</a> 
                <p>Donne ({{count($effettivi->donne)}})</p>
                <p>Uomini  ({{count($effettivi->uomini)}})</p>
                <span class="badge badge-primary badge-pill"> {{$effettivi->total}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{route('nomadelfia.popolazione.posizione.postulanti')}}">  Postulanti</a> 
                <p>Donne ({{count($postulanti->donne)}})</p>
                <p>Uomini  ({{count($postulanti->uomini)}})</p>
                <span class="badge badge-primary badge-pill"> {{$postulanti->total}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{route('nomadelfia.popolazione.posizione.ospiti')}}">  Ospiti</a> 
                <p>Donne ({{count($ospiti->donne)}})</p>
                <p>Uomini  ({{count($ospiti->uomini)}})</p>
                <span class="badge badge-primary badge-pill"> {{$postulanti->total}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
               <a href="{{route('nomadelfia.popolazione.posizione.effettivi')}}">  Sacerdoti</a> 
                <span class="badge badge-primary badge-pill"> {{count($sacerdoti)}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
               <a href="{{route('nomadelfia.popolazione.posizione.effettivi')}}">  Mamme di Vocazione</a> 
                <span class="badge badge-primary badge-pill"> {{count($mvocazione)}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
               <a href="{{route('nomadelfia.popolazione.posizione.effettivi')}}">  Nomadelfa Mamma</a> 
                <span class="badge badge-primary badge-pill"> {{count($nomanamma)}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
               <a href="{{route('nomadelfia.popolazione.posizione.figli.maggiorenni')}}">  Figli Maggiorenni</a> 
                <p>Donne ({{count($maggiorenni->donne)}})</p>
                <p>Uomini  ({{count($maggiorenni->uomini)}})</p>
                <span class="badge badge-primary badge-pill"> {{$maggiorenni->total}}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
               <a href="{{route('nomadelfia.popolazione.posizione.effettivi')}}">  Figli Minorenni</a> 
                <p>Donne ({{count($minorenni->donne)}})</p>
                <p>Uomini  ({{count($minorenni->uomini)}})</p>
                <span class="badge badge-primary badge-pill"> {{$minorenni->total}}</span>
            </li>
        </ul>
      </div>
      <div class="card-footer">
       <a href="{{ route('nomadelfia.persone') }}" class=" text-center  btn btn-primary">Entra</a> 
      </div>
    </div>

    <div class="card" >
     <div class="card-header">
        Gestione Famiglie
      </div>
      <div class="card-body">
          <ul>
              @foreach ($posizioniFamiglia as $posizione)
                  <li>{{$posizione->posizione_famiglia}} :   <strong>  {{$posizione->count}}</strong>  
                     @if($posizione->sesso == 'F') 
                      <span class="badge badge-primary"> {{$posizione->sesso}}</span>
                     @else
                     <span class="badge badge-warning"> {{$posizione->sesso}}</span>
                    @endif
                  </li>
              @endforeach
          </ul>
      </div>
      <div class="card-footer">
      <a href="{{ route('nomadelfia.famiglie') }}"class="btn btn-primary">Entra</a>
      </div>
    </div>

    <div class="card " >
      <div class="card-header">
        Gestione Gruppi Familiari
      </div>
      <div class="card-body">
          <ul>
              @foreach ($gruppi as $gruppo)
                  <li>
                      @include('nomadelfia.templates.gruppo', ['id'=>$gruppo->id, "nome"=>$gruppo->nome])
                          <span class="badge badge-primary badge-pill"> {{count($gruppo->componenti())}}</span>
                  </strong>  
                  
                  </li>
              @endforeach
          </ul>
      </div>
      <div class="card-footer">
         
        <a href="{{ route('nomadelfia.gruppifamiliari') }}"class="btn btn-primary">Entra </a>
      </div>
    </div>
  </div> <!-- end card deck-->
  <div class="card-deck my-2">
    
   <div class="card ">
      <div class="card-header">
        Gestione Aziende
      </div>
      <div class="card-body">
      </div>
      <div class="card-footer">
        <a href="{{ route('nomadelfia.aziende') }}"class="btn btn-primary">Entra</a>
      </div>
    </div>
    
    <div class="card " >
      <div class="card-header">
        Scuola Familiare
      </div>
      <div class="card-body">
        <p class="card-text">
        </p>
      </div>
      <div class="card-footer">
      <a href="{{ route('nomadelfia.famiglie') }}"class="btn btn-primary">Entra</a>
      </div>
    </div>
  </div> <!-- end card deck-->

<!-- <a href="{{ route('nomadelfia.popolazione.stampa') }}" class="btn btn-info my-2">Stampa Popolazione</a>  -->
<!-- <a href="{{ route('nomadelfia.popolazione.anteprima') }}" class="btn btn-info my-2">Anteprima stampa</a>  -->

   <my-modal modal-title="Stampa elenchi" button-title="Stampa Popolazione Nomadelfia" button-style="btn-success my-2">
      <template slot="modal-body-slot">
      <form class="form" method="POST"  id="formStampa" action="{{ route('nomadelfia.popolazione.stampa') }}" >      
          {{ csrf_field() }}
        <h5>Seleziona gli elenchi da stampare:</h5>
       <div class="form-check">
          <input class="form-check-input" type="checkbox" value="personeeta" id="defaultCheck1"   name="elenchi[]" checked>
          <label class="form-check-label" for="defaultCheck1">
            Persone eta
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="personestati" id="defaultCheck1"   name="elenchi[]" checked>
          <label class="form-check-label" for="defaultCheck1">
            Persone stati
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="personeposizioni" id="defaultCheck1"   name="elenchi[]" checked>
          <label class="form-check-label" for="defaultCheck1">
            Persone posizioni
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="aziende" id="defaultCheck2"   name="elenchi[]" checked>
          <label class="form-check-label" for="defaultCheck2">
            Aziende
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="gruppi" id="defaultCheck2"   name="elenchi[]" checked>
          <label class="form-check-label" for="defaultCheck2">
            Gruppi familiari
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="famiglie" id="defaultCheck2"   name="elenchi[]" checked>
          <label class="form-check-label" for="defaultCheck2">
            Famiglie
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="scuola" id="defaultCheck2"   name="elenchi[]" checked>
          <label class="form-check-label" for="defaultCheck2">
            Scuola
          </label>
        </div>
        </form>
      </template> 
      <template slot="modal-button">
        <button class="btn btn-success" form="formStampa">Salva</button>
      </template>
    </my-modal> 

</div>
@endsection
