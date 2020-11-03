@extends('nomadelfia.index')

@section('title', 'Gestione Nomadelfia')


@section('archivio')

 <div class="card-deck">

    <div class="card ">
      <div class="card-header">
        Gestione Popolazione
      </div>
      <div class="card-body">
        
        <h5 class="card-text">  Totale Popolazione Nomadelfia: <strong>{{$totale}}</strong> </h5>

        <div class="row align-items-center">
          <div class="col-md-6"><strong> <a href="{{route('nomadelfia.popolazione.posizione.effettivi')}}">  Effettivi</a> </strong> </div>
          <div class="col-md-6"> <strong> {{$effettivi->total}}</strong>  </div>
          <div class="col-md-6"><p>Uomini</p> </div>
          <div class="col-md-6">  {{count($effettivi->uomini)}} </div>
          <div class="col-md-6"><p>Donne</p> </div>
          <div class="col-md-6">  {{count($effettivi->donne)}} </div>
        </div>
        <div class="row">
            <div class="col-md-6"><strong> <a href="{{route('nomadelfia.popolazione.posizione.postulanti')}}">  Postulanti</a> </strong> </div>
            <div class="col-md-6"> <strong> {{$postulanti->total}}</strong>  </div>
            <div class="col-md-6"><p>Uomini</p> </div>
            <div class="col-md-6">  {{count($postulanti->uomini)}} </div>
            <div class="col-md-6"><p>Donne</p> </div>
            <div class="col-md-6">  {{count($postulanti->donne)}} </div>
        </div>
        <div class="row">
            <div class="col-md-6"><strong> <a href="{{route('nomadelfia.popolazione.posizione.effettivi')}}">  Ospiti</a> </strong> </div>
            <div class="col-md-6"> <strong> {{$ospiti->total}}</strong>  </div>
            <div class="col-md-6"><p>Uomini</p> </div>
            <div class="col-md-6">  {{count($ospiti->uomini)}} </div>
            <div class="col-md-6"><p>Donne</p> </div>
            <div class="col-md-6">  {{count($ospiti->donne)}} </div>
        </div>

          {{-- <ul>
              <li> <a href="{{route('nomadelfia.popolazione.posizione.figli')}}">  Figli</a>   <strong> {{count($figli)}}</strong>    </li>
          </ul> --}}
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
                  <li>{{$gruppo->nome}}:  <strong> {{$gruppo->componenti}}</strong>  </li>
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
