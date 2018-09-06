@extends('layouts.app')

@section('archivio')
   
  <sin-header title="GESTIONE PATENTI">
  </sin-header>
  <div class="table-responsive">
   <table class="table table-hover table-bordered table-sm"  style="table-layout:auto;overflow-x:scroll;">
    <thead class="thead-inverse">
        <tr>
            <th> Nominativo</th>
            <th> Numero Patente</th>
            <th> Categoria  </th>
            <th> Data Scadenza </th>
        </tr>
    </thead>
    <tbody>
         @foreach($viewdata as $record)
          <tr hoverable>
              <td> {{$record->persone->nominativo}}</td>
              <td> {{$record->numero_patente}}</td>
              <td>
               @foreach($record->categorie as $categoria)
               {{$categoria->patente_categoria}}&nbsp;&nbsp;
               @endforeach
              </td>
              <td> {{$record->data_scadenza_patente}}</td>
              <td>
                <div class='btn-group' role='group' aria-label="Basic example">
                <a class="btn btn-warning" href="{{ route('patente.modifica', $record->numero_patente) }}">Modifica</a>
                </div>
              </td>
          </tr>
         @endforeach
    </tbody>
   </table>
   <div class='btn-group' role='group' aria-label="Basic example">
            <a class="btn btn-warning" href="{{ route('patente.inserimento') }}">Inserisci nuova patente</a>
   </div>
   <div class="col-md-2 offset-md-5">
      {{ $viewdata->links("pagination::bootstrap-4") }}
   </div>
  </div>

@endsection
