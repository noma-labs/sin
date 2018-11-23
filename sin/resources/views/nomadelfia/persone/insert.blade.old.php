@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi Persona'])
<form class="form" method="POST">

{{ csrf_field() }}
<!-- Dati anagrafici -->
<h4>Dati Anagrafici</h4>
<hr>
<div class="form-row">
  <div class="col-md-3">
    <div class="form-group">
      <label for="nome">Nome</label>
      <input type="text" class="form-control" id="nome" placeholder="Nome" name="nome" value="{{ old('nome') }}" >
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label for="cognome">Cognome</label>
      <input type="text" class="form-control" id="cognome" name="cognome" placeholder="Cognome" value="{{ old('cognome') }}">
    </div>
  </div>
  <div class="col-md-1">
    <div class="form-group">
      <label for="sesso">Sesso</label>
      <select class="form-control" id="sesso" name="sesso">
        <option value="" hidden></option>
        <option value="M" @if(old('sesso')=='M') selected @endif>M</option>
        <option value="F" @if(old('sesso')=='F') selected @endif>F</option>
      </select>
    </div>
  </div>
  <div class="col-md-2">
    <div class="form-group">
      <label for="nascita">Data di Nascita</label>
      <input type="date" class="form-control" id="nascita" name="nascita" value="{{ old('nascita') }}">
    </div>
  </div>
  <div class="col-md-2">
    <div class="form-group">
      <label for="citta">Città di Nascita</label>
      <input type="text" class="form-control" id="citta" name="citta" value="{{ old('citta') }}">
    </div>
  </div>
  <div class="col-md-1">
    <div class="form-group">
      <label for="provincia">Provincia</label>
      <select class="form-control">
        @foreach($provincie as $provincia)
          <option value="{{ $provincia->provincia }}">{{ $provincia->sigla }}</option>
        @endforeach
      </select>
    </div>
  </div>
</div>

<h4>Dati interni Nomadelfia</h3>
<hr>
<!-- Dati interni Nomadelfia -->
<div class="form-row">
  <div class="col-md-3">
    <div class="form-group">
      <label for="nominativo">Nominativo</label>
      <input type="text" class="form-control" id="nominativo" name="nominativo" placeholder="Nominativo" data-toggle="tooltip" title="nominativo utilizzato internamente per la persona. es: Matteo L." value="{{ old("nominativo") }}">
    </div>
  </div>

  <div class="form-group col-md-3">
      <label for="inputState">Posizione in Nomadelfia</label>
      <select id="inputState" class="form-control" name="posizione">
        <option selected hidden value="">Seleziona...</option>
        @foreach($posizioni as $posizione)
        <option value="{{ $posizione->id }}" @if(old('posizione') == string($posizione->id)) selected @endif>{{ $posizione->nome }}</option>
        @endforeach
      </select>
  </div>
  
  <div class="col-md-2">
    <div class="form-group">
      <label for="inizio">Data inizio</label>
      <input type="date" class="form-control" id="inizio" name="inizio" value="{{ old('inizio') }}">
    </div>
  </div>

<div class="form-group col-md-2">
  <label for="gruppo">Gruppo Familiare</label>
  <select class="form-control" id="gruppo" name="gruppo">
    <option selected hidden value="">Seleziona...</option>
    @foreach($gruppi as $gruppo)
      <option value="{{ $gruppo->id }}" @if(old('gruppo')== $gruppo->id) selected @endif>{{ $gruppo->nome }}</option>
    @endforeach
  </select>
</div>

<div class="col-md-2">
    <div class="form-group">
      <label for="data_gruppo">Data cambio gruppo</label>
      <input type="date" class="form-control" id="data_gruppo" name="data_gruppo" value="{{ old('data_gruppo') }}">
    </div>
  </div>
</div>
<!-- fine seconda riga -->

<div class="form-row">
  <div class="form-group col-md-3">
    <label for="famiglia">Famiglia di Appartenenza</label>
    <div class="input-group">
      <select id="famiglia" class="custom-select" name="famiglia">
        <option selected hidden value="">Seleziona...</option>
        @foreach($famiglie as $famiglia)
        <option value="{{ $famiglia->id }}" @if(old('famiglia')== $famiglia->id) selected @endif>{{ $famiglia->nome_famiglia }}</option>
        @endforeach
      </select>
      <div class="input-group-append">
        <button class="btn btn-outline-primary" type="button" data-toggle="modal" data-target="#nuovaFamiglia">Nuova</button>
      </div>
    </div>
  </div>

  <div class="form-group col-md-3">
    <label for="nucleo">Posizione nucleo famigliare</label>
    <select class="form-control" id="nucleo" name="nucleo">
      <option selected hidden value="">Seleziona...</option>
      @foreach($nuclei_famigliari as $nucleo)
        <option value="{{ $nucleo->id }}" @if(old('nucleo')== $nucleo->id) selected @endif>{{ $nucleo->nome }}</option>
      @endforeach
    </select>
  </div>

</div>
<!-- fine terza riga -->

<h4>Opzionali</h4>
<hr>
<div class="form-row">
  <div class="form-group col-md-3">
    <label for="azienda">Azienda</label>
    <select class="form-control" id="azienda" name="azienda">
      <option value="" selected>Seleziona...</option>
      @foreach($aziende as $azienda)
      <option value="{{ $azienda->id }}" @if(old('azienda')== $azienda->id) selected @endif>{{ strtoupper($azienda->nome_azienda) }}</option>
      @endforeach
    </select>
  </div>  

  <div class="col-md-2">
    <div class="form-group">
      <label for="data_lavoro">Inizio lavoro</label>
      <input type="date" class="form-control" id="data_lavoro" name="data_lavoro" value="{{ old('data_lavoro') }}">
    </div>
  </div>

  <div class="form-group col-md-3">
    <label for="incarico">Incarico</label>
    <select class="form-control" id="incarico" name="incarico">
      <option value="" selected>Seleziona...</option>
      @foreach($incarichi as $incarico)
      <option value="{{ $incarico->id }}" @if(old('incarico')== $incarico->id) selected @endif>{{ strtoupper($incarico->nome) }}</option>
      @endforeach
    </select>
  </div>  
  
  <div class="col-md-2">
    <div class="form-group">
      <label for="data_incarico">Inizio incarico</label>
      <input type="date" class="form-control" id="data_incarico" name="data_incarico" value="{{ old('data_incarico') }}">
    </div>
  </div>

</div>
<div class="form-row">
  <div class="col-md-2 offset-md-10">
    <button type="submit" class="btn btn-block btn-primary">Salva</button>
    <button class="btn btn-success" name="_addanother" value="true" type="submit">Salva e aggiungi un'altro </button>
   <button class="btn btn-success" name="_addonly" value="true" type="submit">Salva</button>
  </div>
</div>
</form>
<br>

<!-- Modal -->
<div class="modal fade" id="nuovaFamiglia" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nuova Famiglia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post">
          <label for="nome_famiglia">Nome nuova famiglia</label>
          <div class="input-group mb-3">
            <input class="form-control" type="text" name="nuova_famiglia" id="nome_famiglia">
            <div class="input-group-append">
              <button class="btn btn-outline-primary" type="submit" id="button-addon2">Salva</button>
        
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection