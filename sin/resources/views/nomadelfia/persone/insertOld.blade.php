@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi Persona'])


  <famiglia-select api_url="{{ route('api.nomadeflia.famiglie') }}"  old="{{ old('famiglia') }}">
    
    <template slot="csrf">
      {{ csrf_field() }}
    </template>
    

    <template slot="provincie">
      @foreach($provincie as $provincia)
          <option value="{{ $provincia->provincia }}">{{ $provincia->sigla }}</option>
<<<<<<< HEAD
      @endforeach
    </template>

    <template slot="posizione">
      @foreach($posizioni as $posizione)
          <option value="{{ $posizione->id }}" @if(old('posizione') == string($posizione->id)) selected @endif>{{ $posizione->descrizione_posizione }}</option>
=======
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
>>>>>>> aae0bc1b931c7486b3bb63b4ee660df0177fdc74
      @endforeach
    </template>

    <template slot="gruppi">
      @foreach($gruppi as $gruppo)
        <option value="{{ $gruppo->id }}" @if(old('gruppo')== $gruppo->id) selected @endif>{{ $gruppo->nome_gruppo }}</option>
      @endforeach
    </template>

    <template slot="aziende">
      @foreach($aziende as $azienda)
        <option value="{{ $azienda->id }}" @if(old('azienda')== $azienda->id) selected @endif>{{ strtoupper($azienda->nome_azienda) }}</option>
      @endforeach
    </template>

    <template slot="incarichi">
      @foreach($incarichi as $incarico)
      <option value="{{ $incarico->id }}" @if(old('incarico')== $incarico->id) selected @endif>{{ strtoupper($incarico->nome) }}</option>
      @endforeach
    </template>

  </famiglia-select>

<br>

@endsection
