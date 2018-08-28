@extends('layouts.app')

@section('title', 'Gestione Nomadelfia')

@section('navbar-link')
<li class="nav-item">
    <a class="nav-link" href="{{ route('nomadelfia') }}">Nomadelfia</a>
</li>
@endsection


@section('archivio')
<div class="row">
  <div class="col-md-4">
    <div class="card mb-3">
      {{-- <div class="card-header">Popolazione</div> --}}
      <div class="card-body">
        <h3 class="card-title">Gestione Persone</h3>
        <p class="card-text">
           {{-- <strong>{{App\Nomadelfia\Models\Persona::presente()->count()}}</strong> persone presenti --}}
        </p>
        <a href="{{ route('persone.inserimento') }}" class="btn btn-primary">Entra</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <h3 class="card-title">Aziende</h3>
        <p class="card-text">
        </p>
        <a href="{{ route('nomadelfia.aziende') }}"class="btn btn-primary">Gestione Aziende </a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card" >
      <div class="card-body">
        <h3 class="card-title">Gruppi Familiari</h3>
        <p class="card-text">
        </p>
        <a href="{{ route('nomadelfia.gruppifamiliari') }}"class="btn btn-primary">Gestione Gruppi Familiari </a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card" >
      <div class="card-body">
        <h3 class="card-title">Famiglie</h3>
        <p class="card-text">
        </p>
        <a href="{{ route('nomadelfia.gruppifamiliari') }}"class="btn btn-primary">Gestione Famiglie </a>
      </div>
    </div>
  </div>
</div>
@endsection
