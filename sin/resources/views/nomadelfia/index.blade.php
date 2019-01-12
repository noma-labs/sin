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
      <a class="dropdown-item" href="{{route('nomadelfia.autocomplete.persona')}}">Ricerca Persona</a>
    </div>
  </li>
  <li class="nav-item">
     <a class="nav-link"href="{{ route('nomadelfia.gruppifamiliari') }}">Gruppi familiari</a>
  </li>
  <!-- <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle"  id="navbarPesone" role="button" 
    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Gruppi Familiari
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarPesone">
      <a class="dropdown-item" href="{{ route('nomadelfia.gruppifamiliari') }}" >Gestione Gruppi familiari</a> 
    </div>
  </li> -->
  <li class="nav-item">
     <a class="nav-link"  href="{{ route('nomadelfia.aziende') }}">Aziende</a>
  </li>
  <!-- <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle"  id="navbarPesone" role="button" 
    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Aziende
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarPesone">
      <a class="dropdown-item" href="{{ route('nomadelfia.aziende') }}" >Gestione Aziende</a> 
    </div>
  </li> -->
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
          Totale: <strong>{{App\Nomadelfia\Models\Persona::presente()->count()}}</strong> </p>
          <ul>
          <li>Donne maggiorenni: <strong> {{App\Nomadelfia\Models\Persona::presente()->donne()->maggiorenni()->count()}}</strong> </li>
          <li>Uomini maggiorenni: <strong> {{App\Nomadelfia\Models\Persona::presente()->uomini()->maggiorenni()->count()}}</strong> </li>
          <li>Figli Minorenni: <strong> {{App\Nomadelfia\Models\Persona::presente()->minorenni()->count()}}</strong> </li>
          </ul>

         <p class="card-text">
         Effettivi:  <strong>  {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->count()}}</strong> 
         Postulanti:  <strong>  {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->count()}}</strong> 
          Figli:  <strong>  {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->count()}}</strong> 
          Ospiti:  <strong>  {{App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->count()}}</strong> 
          </p>
        <a href="{{ route('nomadelfia.persone') }}" class=" text-center  btn btn-primary">Entra</a> 
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
      </div>
    </div>
  </div>
</div>

<a href="{{ route('nomadelfia.popolazione.stampa') }}" class=" text-center  btn btn-danger">Stampa popolazione nomadelfia</a> 
<a href="{{ route('nomadelfia.popolazione.anteprima') }}" class=" text-center  btn btn-danger">anteprima stampa</a> 

</div>
@endsection
