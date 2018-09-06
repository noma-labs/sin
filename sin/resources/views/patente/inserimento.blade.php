@extends('layouts.app')

@section('archivio')
   
  <sin-header title="Modifica Patente">
  </sin-header>
<form class="container-fluid" id="needs-validation" method="POST" action="{{ route('patente.inserimento.conferma') }}">
   {{ csrf_field() }}
   <div class="form-group">
   <label for="persona">Persona</label>
      <select class="form-control" id="persona" name="persona">
        @foreach ($persone as $persona)
          <option value="{{ $persona->id }}">{{ $persona->nominativo }}</option>
        @endforeach
      </select>
  </div>
  <div class="form-group">
    <label for="numero_patente">Numero Patente:</label>
    <input type="text" class="form-control" id="numero_patente" name="numero_patente" value="inserire numero patente">
  </div>
  <div class="form-group">
    <label for="data_nascita_persona_patente">Data di nascita:</label>
    <input type="text" class="form-control" id="data_nascita_persona_patente" name="data_nascita_persona_patente" value="inserire data nascita">
  </div>
  <div class="form-group">
    <label for="luogo_nascita_persona_patente">Luogo di nascita:</label>
    <input type="text" class="form-control" id="luogo_nascita_persona_patente" name="luogo_nascita_persona_patente" value="inserire luogo nascita">
  </div>
  <div class="form-group">
    <label for="rilascio_pantente">Rilascio:</label>
    <input type="text" class="form-control" id="rilascio_pantente" name="rilascio_pantente" value="inserire luogo nascita">
  </div>
  <div class="form-group">
    <label for="data_inizio_patente">Data inizio patente:</label>
    <input type="text" class="form-control" id="data_inizio_patente" name="data_inizio_patente" value="inserire data inizio patente">
  </div>
  <div class="form-group">
    <label for="data_scadenza_patente">Data scadenza patente:</label>
    <input type="text" class="form-control" id="data_scadenza_patente" name="data_scadenza_patente" value="inserire data scadenza patente">
  </div>
  <div class="form-group">
      <label for="categoria_patente">Categoria patente</label>
      <select class="form-control" id="categoria_patente" name="categoria_patente">
        <option value="-1">nessuna categoria</option>
        @foreach ($categorie as $categoria)
          <option value="{{ $categoria->id }}">{{ $categoria->patente_categoria }} {{$categoria->descrizione_categoria}}</option>
        @endforeach
      </select>
    </div>
  <div class="form-group">
    <label for="note">Note:</label>
    <textarea class="form-control" id="note" name="note"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Invia</button>
</form>
@endsection
