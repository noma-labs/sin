@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi Persona'])

<!-- Dati anagrafici -->
<div class="row">
  <div class="col-md-6 offset-md-3">
  <form method="POST" action="{{route('nomadelfia.persone.inserimento.confirm')}}">
    {{ csrf_field() }}
    <div class="form-group row">
      <label for="fornominativo" class="col-sm-6 col-form-label">Nominativo:</label>
      <div class="col-sm-6">
        <input class="form-control" id="fornominativo" name="nominativo"  value="{{ old('nominativo') }}" placeholder="Nominativo">
      </div>
    </div>
    <div class="form-group row">
      <label for="fornome" class="col-sm-6 col-form-label">Nome:</label>
      <div class="col-sm-6">
        <input  class="form-control" id="fornome" name="nome" value="{{ old('nome') }}" placeholder="Nome">
      </div>
    </div>
    <div class="form-group row">
      <label for="forcognome" class="col-sm-6 col-form-label">Cognome:</label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="forcognome" name="cognome" placeholder="Cognome" value="{{ old('cognome') }}">
      </div>
    </div>
    
    <div class="form-group row">
      <label for="fornascita" class="col-sm-6 col-form-label">Data di Nascita:</label>
      <div class="col-sm-6">
        <input class="form-control" id="fornascita" name="data_nascita" placeholder="Data di nascita"  value="{{ old('data_nascita') }}">
      </div>
    </div>
    <div class="form-group row">
      <label for="forluogo" class="col-sm-6 col-form-label">Luogo di nascita:</label>
      <div class="col-sm-6">
        <input  class="form-control" id="forluogo" placeholder="Luogo di nascita" name="luogo_nascita" value="{{ old('luogo_nascita') }}">
      </div>
    </div>
    <fieldset class="form-group" >
      <div class="row">
        <legend class="col-form-label col-sm-6 pt-0">Sesso:</legend>
        <div class="col-sm-6">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="sesso" id="forsessoM" value="M" @if(old('sesso')=='M') checked @endif>
              <label class="form-check-label" for="forsessoM">
                Maschio
              </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="sesso" id="forsessoF"  value="M" @if(old('sesso')=='F') checked @endif>
            <label class="form-check-label" for="forsessoF">
              Femmina
            </label>
          </div>
        </div>
      </div>
    </fieldset>

    <div class="row">
      <div class="col-auto">
        <button class="btn btn-warning" name="_addanother" value="true" type="submit">Salva e aggiungi un'altro </button>
        <button class="btn btn-success" name="_addonly" value="true" type="submit">Salva e visualizza</button> 
      </div>
    </div>
  </form>
  </div>
</div>
@endsection