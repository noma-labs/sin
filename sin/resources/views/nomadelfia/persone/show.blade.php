@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Persona'])

<div class="row">
  <div class="col-md-6">
    <form>
      <div class="form-group">
        <label for="nominativo">Nominativo</label>
        <input type="text" readonly class="form-control" id="nominativo" placeholder="{{$persona->nominativo}}">
      </div>
      <div class="form-group">
        <label for="nominativo">Nome</label>
        <input type="text" readonly class="form-control" id="nominativo" placeholder="{{$persona->datipersonali->nome}}">
      </div>
      <div class="form-group">
        <label for="nominativo">Cognome</label>
        <input type="text" readonly class="form-control" id="nominativo" placeholder="{{$persona->datipersonali->cognome}}">
      </div>
      <div class="form-group">
        <label for="datanascita">Data Nascita</label>
        <input type="text" readonly class="form-control" id="datanascita" placeholder="{{$persona->data_nascita_persona}}">
      </div>
      <div class="form-group">
        <label >Famiglia</label>
        <select class="form-control">
          <option>Suo-famiglia</option>
        </select>
      </div>
    </form>
  </div>

  <div class="col-md-6">
    <form>
      <div class="form-group">
        <label >Posizione</label>
        <select class="form-control">
          @foreach (App\Nomadelfia\Models\Posizione::all() as $posizione)
              <option >{{ $posizione->nome }} ({{ $posizione->abbreviato }})</option>
          @endforeach
        </select>
      </div>
      <label>Stato</label>
        <select class="form-control">
          @foreach (App\Nomadelfia\Models\Stato::all() as $stato)
            <option >{{ $stato->nome }} ({{ $stato->stato }})</option>
         @endforeach
        </select>
      <div class="form-group">
        <label >Gruppo Familiare</label>
          <select class="form-control">
          @foreach (App\Nomadelfia\Models\GruppoFamiliare::all() as $gruppo)
              <option >{{ $gruppo->nome }}</option>
          @endforeach
         </select>
      </div>
      <div class="form-group">
        <label >Azienda</label>
          <select class="form-control">
          @foreach (App\Nomadelfia\Models\Azienda::all() as $azienda)
              <option >{{ $azienda->nome_azienda }}</option>
          @endforeach
         </select>
      </div>
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="exampleCheck1">
        <label class="form-check-label" for="exampleCheck1">Presente in Nomadelfia</label>
      </div>
    </form>
  </div>
</div>
@endsection
