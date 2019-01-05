@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi Persona'])

<div class="row">
  <div class="col-md-6 offset-md-3">
  <form method="POST" action="{{route('nomadelfia.persone.inserimento.initial')}}">
    {{ csrf_field() }}
    <div class="row">
    <div class="col-md-4">
       <label for="fornominativo">Nominativo</label>
        <input  class="form-control" id="fornominativo" name="nominativo" value="{{ old('nominativo') }}" placeholder="---Inserisci Nominativo---">
     </div>
     <div class="col-md-4">
       <label for="fornome">Nome</label>
        <input  class="form-control" id="fornome" name="nome" value="{{ old('nome') }}" placeholder="---Inserisci Nome---">
     </div>
     <div class="col-md-4">
       <label for="fornome">Cognome</label>
        <input  class="form-control" id="fornome" name="cognome" value="{{ old('cognome') }}" placeholder="---Inserisci Cognome---">
     </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <button class="btn btn-success pull-right mt-2" type="submit">Cerca persone esistenti</button>
      </div>
    </div>
  </form>
  </div>
</div>
@endsection