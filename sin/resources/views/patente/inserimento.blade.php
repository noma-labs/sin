@extends('patente.index')

@section('archivio')
   
<sin-header title="Inserisci nuova Patente"> </sin-header>

<patente-inserimento
            api-officina-persone="{{route('api.officina.clienti')}}"
            api-patente-categorie="{{route('api.patente.categorie')}}"
            api-patente-create="{{route('api.patente.create')}}" >
</patente-inserimento>

<!-- <form class="container-fluid"  method="POST" action="{{ route('patente.inserimento.conferma') }}">
   {{ csrf_field() }}
  <div class="row">
   <div class="col-md-6">
    <div class="row">
      <div class="col-md-12">
        <label for="numero_patente">Persona:</label>
        <autocomplete
            placeholder="Inserisci nominativo..."
            name="persona_id"
            url={{route('api.officina.clienti')}}>
        </autocomplete>
      </div>
    </div> <!- - end zero row in left colum- ->
    <div class="row">
      <div class="col-md-6">
        <label for="data_nascita">Data di nascita:</label>
        <input type="text" class="form-control" id="data_nascita" name="data_nascita" >
      </div>
      <div class="col-md-6">
        <label for="luogo_nascita">Luogo di nascita:</label>
        <input type="text" class="form-control" id="luogo_nascita" name="luogo_nascita" >
      </div>
    </div><!- - end first row in left colum- ->
    <div class="row">
      <div class="col-md-6">
        <label for="data_rilascio_patente">Patente rilasciata il:</label>
        <input type="text" class="form-control" id="data_rilascio_patente" name="data_rilascio_patente" >
      </div>
      
      <div class="col-md-6">
        <label for="rilasciata_dal">Rilascia da:</label>
        <input type="text" class="form-control" id="rilasciata_dal" name="rilasciata_dal" >
      </div>
      
     </div><!- - end secoond row in left colum- ->
     <div class="row">
      <div class="col-md-6">
          <label for="data_scadenza_patente">Patente valida fino al:</label>
          <input type="text" class="form-control" id="data_scadenza_patente" name="data_scadenza_patente" >
        </div>
      <div class="col-md-6">
        <label for="numero_patente">Numero Patente:</label>
        <input type="text" class="form-control" id="numero_patente" name="numero_patente" >
      </div>
     </div><!- - end third row in left colum- ->
     <div class="row">
      <div class="form-group col-md-9">
        <label for="note">Note:</label>
        <textarea class="form-control" id="note" name="note"></textarea>
      </div>
      <div clas="form-group  col-md-3">
        <label for="button">&nbsp;</label>
        <button type="submit" id="button" form="edit-patente" class="btn btn-primary">Salva</button>
      </div>
     </div> <!- - end fouth row in left colum- ->
   </div>  <!- - end left column- ->

   </form>
   <div class="col-md-6">
   <patente-categorie-edit>
   </div>  <!- - end  rigth column - ->
  </div> <!--end first row - ->
-->
  
@endsection