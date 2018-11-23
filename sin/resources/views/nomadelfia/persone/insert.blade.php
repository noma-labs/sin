@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi Persona'])

<!-- Dati anagrafici -->
<div class="row">
@isset($personeEsistenti)
  <div class="col-md-8 offest-md-2 table-responsive">
    <table class="table table-hover table-bordered table-sm"  style="table-layout:auto;overflow-x:scroll;">
      <thead class="thead-inverse">
        <tr>
          <th width="7%">Nominativo</th>
          <th width="10%">Nome</th>
          <th width="6%">Cognome</th>
          <th width="3%">Data Nascita</th>
          <th width="3%">Luogo Nascita</th>
          <th width="8%">sesso</th>
          <th width="10%">Oper.</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($personeEsistenti as $persona)
        <tr hoverable>
          <td>{{ $persona->persona->nominativo }}</td>
          <td>{{ $persona->nome }}</td>
          <td>{{ $persona->cognome }}</td>
          <td>{{ $persona->data_nascita }}</td>
          <td>{{ $persona->prvincia_nascita }}</td>
          <td>{{ $persona->sesso }}</td>

          <td>
            <div class='btn-group' role='group' aria-label="Basic example">
              <a class="btn btn-warning" href="{{ route('nomadelifa.persone.dettaglio', $persona->persona_id) }}">Modifica</a>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div> <!-- fine col existing persone -->
  @endisset
  <div class="col-md-6 offset-md-3">
  <form method="POST" action="{{route('nomadelfia.persone.inserimento.confirm')}}">
    {{ csrf_field() }}
    <div class="form-group row">
      <label for="fornominativo" class="col-sm-6 col-form-label">Nominativo:</label>
      <div class="col-sm-6">
        <input class="form-control" id="fornominativo" name="nominativo" placeholder="Nominativo">
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