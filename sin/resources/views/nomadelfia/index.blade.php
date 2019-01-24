@extends('layouts.app')

@section('title', 'Gestione Nomadelfia')

@section('navbar-link')
  <li class="nav-item">
      <a class="nav-link" href="{{ route('nomadelfia') }}">Nomadelfia</a>
  </li>
  
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle"  id="navbarPesone" role="button" 
    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Persone
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarPesone">
      <a class="dropdown-item" href="{{ route('nomadelfia.persone') }}" >Gestione persone</a> 
      <a class="dropdown-item" href="{{route('nomadelfia.persone.inserimento')}}">Inserisci Persona</a>
    </div>
  </li>
 
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle"  id="navbarFamiglie" role="button" 
      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Famiglie
      </a>
      <div class="dropdown-menu" aria-labelledby="navbarFamiglie">
        <a class="dropdown-item" href="{{ route('nomadelfia.famiglie') }}">Gestione famiglie</a> 
        <a class="dropdown-item" href="{{route('nomadelfia.famiglie.create')}}">Inserisci Famiglia</a>
      </div>
  </li>
  <li class="nav-item">
     <a class="nav-link"href="{{ route('nomadelfia.gruppifamiliari') }}">Gruppi familiari</a>
  </li>
  <li class="nav-item">
     <a class="nav-link"  href="{{ route('nomadelfia.aziende') }}">Aziende</a>
  </li>
@append 


@section('archivio')
<div class="container">
<div class="row">
  <div class="col-md-4">
    <div class="card mb-3">
      <div class="card-header">
        Gestione Persone
      </div>
      <div class="card-body">
        <p class="card-text"> 
          Totale: <strong>{{App\Nomadelfia\Models\Persona::attivo()->count()}}</strong> </p>
          <ul>
          <li>Donne maggiorenni: <strong> {{App\Nomadelfia\Models\Persona::attivo()->donne()->maggiorenni()->count()}}</strong> </li>
          <li>Uomini maggiorenni: <strong> {{App\Nomadelfia\Models\Persona::attivo()->uomini()->maggiorenni()->count()}}</strong> </li>
          <li>Figli Minorenni: <strong> {{App\Nomadelfia\Models\Persona::attivo()->minorenni()->count()}}</strong> </li>
          </ul>

         <p class="card-text">
         Effettivi:  <strong>  {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->count()}}</strong> 
         Postulanti:  <strong>  {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->count()}}</strong> 
          Figli:  <strong>  {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->count()}}</strong> 
          Ospiti:  <strong>  {{App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->count()}}</strong> 
          </p>
        <a href="{{ route('nomadelfia.persone') }}" class=" text-center  btn btn-primary">Entra</a> 
        <!-- <a href="{{ route('nomadelfia.popolazione.stampa') }}" class="text-center btn btn-info">Stampa persone</a>  -->
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        Gestione Aziende
      </div>
      <div class="card-body">
        <p class="card-text">
        </p>
        <a href="{{ route('nomadelfia.aziende') }}"class="btn btn-primary">Entra</a>
        <!-- <a href="{{ route('nomadelfia.popolazione.stampa') }}" class="text-center btn btn-info">Stampa aziende</a>  -->
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card" >
      <div class="card-header">
        Gestione Gruppi Familiari
      </div>
      <div class="card-body">
        <p class="card-text">
        </p>
        <a href="{{ route('nomadelfia.gruppifamiliari') }}"class="btn btn-primary">Entra </a>
        <!-- <a href="{{ route('nomadelfia.popolazione.stampa') }}" class="text-center btn btn-info">Stampa gruppi</a>  -->

      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card" >
    <div class="card-header">
        Gestione Famiglie
      </div>
      <div class="card-body">
        <p class="card-text">
        </p>
        <a href="{{ route('nomadelfia.famiglie') }}"class="btn btn-primary">Entra</a>
        <!-- <a href="{{ route('nomadelfia.popolazione.stampa') }}" class="text-center btn btn-info">Stampa famiglie</a>  -->
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card" >
    <div class="card-header">
        Scuola Familiare
      </div>
      <div class="card-body">
        <p class="card-text">
        </p>
        <a href="{{ route('nomadelfia.famiglie') }}"class="btn btn-primary">Entra</a>
        <!-- <a href="{{ route('nomadelfia.popolazione.stampa') }}" class="text-center btn btn-info">Stampa scuola</a>  -->
      </div>
    </div>
  </div>
</div>

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
