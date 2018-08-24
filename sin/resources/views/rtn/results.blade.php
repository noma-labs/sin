@extends('layouts.app')

@section('title', 'libri')

@section('archivioname', 'Biblioteca Ricerca Libri')

@section('archivio')

  <div class="alert alert-info alert-dismissable fade in"><strong> {{$msgSearch}}</strong>
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  </div>

  <div class="alert alert-success"> Numero di libri Trovati: <strong> {{ $count }} </strong></div>

@if ($count > 1)
<table class='table table-sm  table-striped table-bordered'>
  <thead class='thead-inverse'>
  <tr>
    <th>COLLOC.</th>
    <th>TITOLO</th>
    <th>AUTORE</th>
    <th>EDITORE</th>
    <th>CLASSIFICAZIONE</th>
    <th>NOTE</th>
    </tr>
  </thead>
  <tbody>

  @forelse ($libri as $libro)
      <tr>
        <td width="10">{{ $libro->COLLOCAZIONE }}</td>
        <td width="90">{{ $libro->TITOLO }}</td>
        <td width="20" >{{ $libro->AUTORE }}</td>
        <td width="20" >{{ $libro->EDITORE }}</td>
        <td width="80" >{{ $libro->DESCRIZIONE }}</td>
        <td width="50" >{{ $libro->NOTE }}</td>
      </tr>
  @empty
      <div class="alert alert-danger">
          <strong>Nessun risultato ottenuto</strong>
      </div>
  @endforelse
</tbody>
</table>
@endif

<button class="btn btn-success"  name="inizio"  onClick=toTop()>INIZIO</button>
<button class="btn btn-success"  name="inizio"  onClick=clearSearch()>Nuova Ricerca</button>

@endsection()
