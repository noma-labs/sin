@extends('layouts.app')

@section('archivio')
   
  <sin-header title="Modifica Patente">
  </sin-header>
<form class="container-fluid" id="needs-validation" method="POST" action="{{ route('patente.modifica.conferma', $record->first()->numero_patente) }}">
   {{ csrf_field() }}
  <div class="form-group">
    <label for="numero_patente">Numero Patente:</label>
    <input type="text" class="form-control" id="numero_patente" name="numero_patente" readonly value="{{$record->first()->numero_patente}}">
  </div>
  <div class="form-group">
    <label for="data_nascita">Data di nascita:</label>
    <input type="text" class="form-control" id="data_nascita" name="data_nascita" value="{{ $record->first()->data_nascita }}">
  </div>
  <div class="form-group">
    <label for="luogo_nascita">Luogo di nascita:</label>
    <input type="text" class="form-control" id="luogo_nascita" name="luogo_nascita" value="{{ $record->first()->luogo_nascita }}">
  </div>
  <div class="form-group">
    <label for="rilasciata_dal">Rilascio:</label>
    <input type="text" class="form-control" id="rilasciata_dal" name="rilasciata_dal" value="{{ $record->first()->rilasciata_dal }}">
  </div>
  <div class="form-group">
    <label for="data_rilascio_patente">Data inizio patente:</label>
    <input type="text" class="form-control" id="data_rilascio_patente" name="data_rilascio_patente" value="{{ $record->first()->data_rilascio_patente }}">
  </div>
  <div class="form-group">
    <label for="data_scadenza_patente">Data scadenza patente:</label>
    <input type="text" class="form-control" id="data_scadenza_patente" name="data_scadenza_patente" value="{{ $record->first()->data_scadenza_patente }}">
  </div>
  <div class="form-group">
      <label for="nuova_categoria">Nuova Categoria</label>
      <select class="form-control" id="nuova_categoria" name="nuova_categoria">
        <option value="-1">niente di nuovo</option>
        @foreach ($categorie as $categoria)
           @if(!$record->first()->categorie->contains('id',$categoria->id))
          <option value="{{ $categoria->id }}">{{ $categoria->categoria }} {{$categoria->descrizione}}</option>
           @endif
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
