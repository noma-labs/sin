@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi Persona'])

<!-- Dati anagrafici -->
<div class="row">
@isset($personeEsistenti)
  <div class="col-md-12  table-responsive">
    <table class="table table-hover table-bordered table-sm"  style="table-layout:auto;overflow-x:scroll;">
      <thead class="thead-inverse">
        <tr>
          <th width="10%">Nominativo</th>
          <th width="10%">Nome</th>
          <th width="10%">Cognome</th>
          <th width="10%">Data Nascita</th>
          <th width="10%">Luogo Nascita</th>
          <th width="5%">sesso</th>
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
              <a class="btn btn-success" href="{{ route('nomadelifa.persone.dettaglio', $persona->persona_id) }}">Modifica</a>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div> <!-- fine col existing persone -->
  @endisset
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <a role='button' class="btn btn-warning pull-right" href="{{ route('nomadelfia.persone.inserimento.completo') }}">Aggiungi nuova persona </a>
    </div>
  </div>

@endsection