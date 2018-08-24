@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Persona'])

<div class="row">
  <div class="col-md-4">
    <form>
          <div class="form-group">
            <label for="nominativo">Nominativo</label>
            <input type="text" readonly class="form-control" id="nominativo" placeholder={{$persona->nominativo}}>
          </div>
          <div class="form-group">
            <label for="datanascita">Data Nascita</label>
            <input type="text" readonly class="form-control" id="datanascita" placeholder={{$persona->data_nascita_persona}}>
          </div>
          <div class="form-group">
            <label >Famiglia</label>
            <select class="form-control">
              <option>Suo-famiglia</option>
            </select>
          </div>
          
      </form>
  </div>

  <div class="col-md-4">
    <form>
       <div class="form-group row">
        <label for="staticEmail" class="col-sm-2 col-form-label">Presente:</label>
        <div class="col-sm-10">
          <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="{{$persona->condizione_id}}">
        </div>
      </div>
      <div class="form-group">
        <label >Posizione</label>
        <select class="form-control">
          <option>Effettivo</option>
          <option>Postulante</option>
          <option>Figlio</option>
          <option>Ospite</option>
        </select>
      </div>
      <div class="form-group">
        <label >Gruppo Familiare</label>
        <select class="form-control">
          <option>Suo-gruppo</option>
        </select>
      </div>
      <div class="form-group">
        <label >Azienda</label>
        <select class="form-control">
          <option>Suo-azienda</option>
        </select>
      </div>
    </form>

  </div>
</div>
@endsection
