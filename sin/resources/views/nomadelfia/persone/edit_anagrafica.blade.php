@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Modifica Dati anagrafici'])

<div class="row justify-content-center">
    <div class="col-4">
      <form class="form" method="POST" action="{{ route('nomadelfia.persone.anagrafica.modifica', ['idPersona' =>$persona->id]) }}" >
      {{ csrf_field() }}
        <div class="form-group">
          <label for="xNome">Nome</label>
          <input type="text" class="form-control" name="nome" value="{{old('nome') ? old('nome'): $persona->nome}}">
        </div>
        <div class="form-group">
          <label for="xNome">Cognome</label>
          <input type="text" class="form-control" name="cognome" value="{{old('cognome') ? old('cognome'): $persona->cognome}}">
        </div>
        <div class="form-group">
          <label for="xNominativo">Data Nascita</label>
        <date-picker :bootstrap-styling="true" value="{{old('datanascita') ? old('datanascita'): $persona->data_nascita}}" format="yyyy-MM-dd" name="datanascita"></date-picker>
        </div>
        <div class="form-group">
          <label for="xNome">Luogo Nascita</label>
          <input type="text" class="form-control" name="luogonascita" value="{{old('luogonascita') ? old('luogonascita'): $persona->provincia_nascita}}">
        </div>

        <div class="form-group">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="sesso" id="exampleRadios1" value="M" {{ $persona->sesso == 'M' ? 'checked' : '' }}>
            <label class="form-check-label" for="exampleRadios1">
              Maschio
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="sesso" id="exampleRadios2" value="F"  {{ $persona->sesso == 'F' ? 'checked' : '' }}>
            <label class="form-check-label" for="exampleRadios2">
              Femmina
            </label>
        </div>
        </div>
        <div class="form-group">
          <label for="xBiografia">Biografia</label>
          <textarea class="form-control" id="xBiografia" name="biografia" rows="10">{{$persona->biografia}}</textarea>
        </div>


        <div class="form-group">
          <button class="btn btn-danger">Torna indietro</button>
          <button class="btn btn-success" type="submit">Salva Modifiche</button>
        </div>
      </form>
    </div>
  </div>

@endsection
