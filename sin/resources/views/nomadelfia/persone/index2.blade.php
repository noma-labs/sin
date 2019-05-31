@extends('nomadelfia.index')

@section('archivio') 
<sin-header title="Elenco persone"> Numero persone: </sin-header>

<form method="GET" action="">
   {{ csrf_field() }}

    <button type="submit" class="btn btn-block btn-primary">Ricerca</button>
</form>

@if(!empty($msgSearch))
<div class="alert alert-warning alert-dismissible fade show" role="alert" >Ricerca effettuata:<strong> {{$msgSearch}}</strong>
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
</div>
@endif

@if(!empty($persone))
<div id="results" class="alert alert-success"> Numero di patenti trovate: <strong> {{ $persone->count() }} </strong></div>

<div class="table-responsive">
  <table class="table table-hover table-bordered table-sm"  style="table-layout:auto;overflow-x:scroll;">
    <thead class="thead-inverse">
        <tr>
            <th  style="width: 20%">Nominativo</th>
            <th style="width: 10%"> Nome </th>
            <th style="width: 10%"> Cognome </th>
            <th style="width: 20%"> Sata nascita  </th>
            <th style="width: 20%"> Famiglia </th>
            <th style="width: 10%"> Operazioni </th>
        </tr>
    </thead>
    <tbody>
          @foreach($persone as $persona)
          <tr hoverable>
          <td> {{ $persona->nominativo}} </td>
          <td> {{ $persona->nome}}</td>
          <td>  {{ $persona->cognome}} </td>
          <td>  {{$persona->data_nascita}}</td>
          <td> </td>
          </tr>
          @endforeach
    </tbody>
  </table>
  <div class="col-md-2 offset-md-5">
    {{ $persone->links("pagination::bootstrap-4") }}
  </div>
  </div>
  @endif

@endsection