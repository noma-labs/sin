@extends('patente.index')

@section('archivio')
   
<sin-header title="Inserisci nuova Patente"> </sin-header>


<form class="container-fluid"  method="POST" action="{{ route('patente.inserimento.conferma') }}">
   {{ csrf_field() }}

  <div class="form-row">
    <div class="form-group col-md-3">
    <label>Persona</label>
    <autocomplete placeholder="Inserisci nominativo..." 
                  name="persona_id" 
                  url="{{route('api.officina.clienti')}}">
      </autocomplete>
    </div>
    <div class="form-group col-md-3">
      <label for="data_nascita">Data di nascita:</label>
      <input type="text" class="form-control" id="data_nascita" name="data_nascita" placeholder="inserire data nascita">
    </div>
    <div class="form-group col-md-3">
      <label for="luogo_nascita">Luogo di nascita:</label>
      <input type="text" class="form-control" id="luogo_nascita" name="luogo_nascita" placeholder="inserire luogo nascita">
    </div>
  </div>  <!-- end first row -->

 <div class="form-row">
    <div class="form-group col-md-3">
        <label for="data_rilascio_patente">Patente rilasciata il:</label>
        <input type="text" class="form-control" id="data_rilascio_patente" name="data_rilascio_patente" value="inserire data inizio patente">
    </div>
    <div class="form-group col-md-3">
      <label for="data_scadenza_patente">Validità fino al:</label>
      <input type="text" class="form-control" id="data_scadenza_patente" name="data_scadenza_patente" value="inserire data scadenza patente">
    </div>
    <div class="form-group col-md-3">
      <label for="rilasciata_dal">Rilascia da:</label>
      <input type="text" class="form-control" id="rilasciata_dal" name="rilasciata_dal" placeholder="inserire luogo nascita">
    </div>
    <div class="form-group col-md-3">
      <label for="numero_patente">Numero Patente:</label>
      <input type="text" class="form-control" id="numero_patente" name="numero_patente" placeholder="inserire numero patente">
    </div>
 </div> <!-- end second row -->
  
  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="categoria_patente">Categoria patente</label>
      <select class="form-control" id="categoria_patente" name="categoria_patente">
        <option value="">Seleziona categoria...</option>
        @foreach ($categorie as $categoria)
          <option value="{{ $categoria->id }}">{{ $categoria->categoria }} {{$categoria->descrizione}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group col-md-3">
      <label for="categoria_rilasciata">Rilasciata il:</label>
    </div>
    <div class="form-group col-md-3">
      <label for="categoria_valida">Valida fino al:</label>
    </div>
    <div class="form-group col-md-3">
      <label for="restrizioni">Restrizioni:</label>
    </div>

  </div> <!-- end third row -->
  
  <div class="form-group">
    <label for="note">Note:</label>
    <textarea class="form-control" id="note" name="note"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Invia</button>
</form>
@endsection
