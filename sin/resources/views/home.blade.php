@extends('layouts.app')

@section('archivio')
    <div class="container">
      <div class="row">
        @if (Auth::guest())
        <div class="col-md-4">
          <div class="card text-center border-info mb-3">
            <div class="card-header">
              Biblioteca
            </div>
            <div class="card-body">
              <!-- <h3 class="card-title">Biblioteca</h3> -->
              <p class="card-text">Ricerca libri e video della biblioteca di Nomadelfia.</p>
              <a href="{{ url('/biblioteca')}} " class="btn btn-primary">Accedi come ospite a Biblioteca</a>
            </div>
          </div>
        </div>
        @endif
        @hasanyrole("biblioteca-amm|master|presidenza-amm")
        <div class="col-md-4">
          <div class="card text-center border-info mb-3">
            <div class="card-header">
              Biblioteca
            </div>
            <div class="card-body">
              <!-- <h3 class="card-title">Biblioteca</h3> -->
              <p class="card-text">Gestione libri e video della biblioteca di Nomadelfia.</p>
              <a href="{{ url('/biblioteca')}} " class="btn btn-primary">Accedi a Biblioteca</a>
            </div>
          </div>
        </div>
        @endhasrole
        
        @hasanyrole('rtn|master|presidenza-amm')
            <div class="col-md-4">
              <div class="card text-center border-success mb-3">
                <div class="card-header">
                  Archivio RTN
                </div>
                <div class="card-body">
                  <!-- <h3 class="card-title">RTN</h3> -->
                  <p class="card-text">Gestione archivio Rtn</p>
                  <a href="{{ url('/rtn')}} " class="btn btn-primary">Accedi a RTN</a>
                </div>
              </div>
            </div>
      @endhasrole

      @hasanyrole('meccanica-ope|meccanica-amm|master|presidenza-amm')
        <div class="col-md-4">
          <div class="card text-center border-warning  mb-3">
            <div class="card-header">
              Officina
            </div>
            <div class="card-body">
              <!-- <h3 class="card-title">Officina Meccanica</h3> -->
              <p class="card-text">Gestione mezzi di Nomadelfia</p>
              <a href="{{ route('officina.index') }}" class="btn btn-primary">Accedi  all'officina Meccanica</a>
            </div>
          </div>
        </div>
        @endhasrole

     @hasrole('presidenza-amm|master')
        <div class="col-md-4">
          <div class="card text-center border-warning mb-3">
            <div class="card-header">
              Gestione Nomadelfia
            </div>
            <div class="card-body">
              <p class="card-text">Popolazione Nomadelfia, Aziende, Gruppi familiari, Famiglie </p>
              <a href="{{ route('nomadelfia') }}" class="btn btn-primary">Accedi a Nomadelfia</a>
            </div>
          </div>
        </div>
      @endhasrole
      @hasrole('admin|master')
        <div class="col-md-4">
          <div class="card text-center border-warning mb-3">
            <div class="card-header">
              Amministratore
            </div>
            <div class="card-body">
              <p class="card-text">Pannello di controllo per la gestione degli utenti, permessi, backup e logs di tutti i sitemi.</p>
              <a href="{{ route('admin') }}" class="btn btn-primary">Accedi a Amministratore</a>
            </div>
          </div>
        </div>
      @endhasrole
    </div>

	</div>
@endsection
