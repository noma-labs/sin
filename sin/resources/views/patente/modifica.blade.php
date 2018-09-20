@extends('patente.index')

@section('archivio')
  <sin-header title="Modifica Patente"> </sin-header>
<form class="container-fluid" id="needs-validation" method="POST" action="{{ route('patente.modifica.conferma', $record->numero_patente) }}">
   {{ csrf_field() }}
  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="numero_patente">Persona:</label>
      <autocomplete
          :selected="{{$record->persone()->pluck('nominativo','id')}}"
          placeholder="Inserisci nominativo..."
          name="persona_id"
          url={{route('api.officina.clienti')}}>
      </autocomplete>
    </div>
    <div class="form-group col-md-3">
      <label for="data_nascita">Data di nascita:</label>
      <input type="text" class="form-control" id="data_nascita" name="data_nascita" value="{{ $record->first()->data_nascita }}">
    </div>
    <div class="form-group col-md-3">
      <label for="luogo_nascita">Luogo di nascita:</label>
      <input type="text" class="form-control" id="luogo_nascita" name="luogo_nascita" value="{{ $record->first()->luogo_nascita }}">
    </div>
  </div> <!-- end first row -->

  <div class="form-row">
    <div class="form-group col-md-3">
        <label for="data_rilascio_patente">Patente rilasciata il:</label>
        <input type="text" class="form-control" id="data_rilascio_patente" name="data_rilascio_patente"  value="{{ $record->data_rilascio_patente }}">
    </div>
    <div class="form-group col-md-3">
      <label for="data_scadenza_patente">Validità fino al:</label>
      <input type="text" class="form-control" id="data_scadenza_patente" name="data_scadenza_patente" value="{{ $record->data_scadenza_patente }}">
    </div>
    <div class="form-group col-md-3">
      <label for="rilasciata_dal">Rilascia da:</label>
      <input type="text" class="form-control" id="rilasciata_dal" name="rilasciata_dal" value="{{ $record->rilasciata_dal }}">
    </div>
    <div class="form-group col-md-3">
      <label for="numero_patente">Numero Patente:</label>
      <input type="text" class="form-control" id="numero_patente" name="numero_patente" value="{{ $record->numero_patente }}">
    </div>
 </div> <!-- end second row -->

@foreach($record->categorie as $categoria)
  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="categoria_patente">Categoria patente</label>
      <div>{{$categoria->categoria}}</div>
    </div>
    <div class="form-group col-md-3">
      <label for="categoria_rilasciata">Rilasciata il:</label>
      <div>{{$categoria->pivot->data_rilascio}}</div>
    </div>
    <div class="form-group col-md-3">
      <label for="categoria_valida">Valida fino al:</label>
      <div>{{$categoria->pivot->data_scadenza}}</div>
    </div>
    <div class="form-group col-md-3">
      <label for="restrizioni">Restrizioni:</label>
      <div>{{$categoria->pivot->restrizione_codice}}</div>

    </div>
  </div> <!-- end third row -->
@endforeach


  <div class="form-group">
    <label for="note">Note:</label>
    <textarea class="form-control" id="note" name="note"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Salva</button>
</form>
@endsection
