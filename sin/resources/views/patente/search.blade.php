@extends('patente.index')

@section('archivio') 
<sin-header title="Gestione Patenti"> Numero patenti: {{App\Patente\Models\Patente::count()}}</sin-header>

<form method="GET" action="{{route('patente.ricerca.conferma')}}">
   {{ csrf_field() }}
  <div class="form-row">
    <div class="form-group col-md-3 offset-md-1">
        <label>Persona</label>
        <autocomplete placeholder="---Inserisci nominativo---" 
                      name="persona_id" 
                      url="{{route('api.officina.clienti')}}">
         </autocomplete>
    </div>
    <div class="form-group col-md-3">
      <label for="numero_patente">Numero Patente</label>
      <input  class="form-control" id="numero_patente" name="numero_patente" placeholder="---Inserisci numero patente---">
    </div>
    <div class="form-group col-md-3">
      <label for="categoria_patente">Categoria patente</label>
      <select class="form-control" id="categoria_patente" name="categoria_patente">
        <option selected value="">---Scegli categoria---</option>
        @foreach ($categorie as $categoria)
          <option value="{{ $categoria->id }}">{{ $categoria->categoria }} </option> 
          <!-- {{$categoria->descrizione}}</option> -->
        @endforeach
      </select>
    </div>
  </div>
  <div class="form-row">
      <div class="form-group col-md-2  offset-md-1">
        <label class="control-label">Data Rilascio</label>
        <select class="form-control" name="criterio_data_rilascio" type="text" >
        <option selected value="">---Scegli criterio---</option>
            <option value="<">Minore</option>
            <option value="<=">Minore Uguale</option>
            <option value="=" >Uguale</option>
            <option value=">">Maggiore</option>
            <option value=">=">Maggiore Uguale</option>
        </select>
        </div>
      <div class="col-md-2">
          <div class="form-group">
            <label >&nbsp;</label>
            <input type="date" class="form-control" name="data_rilascio">
          </div>
        </div>
      <div class="form-group col-md-2">
        <label class="control-label">Data Scadenza</label>
        <select class="form-control" name="criterio_data_scadenza" type="text" >
        <option selected value="">---Scegli criterio---</option>
            <option value="<">Minore</option>
            <option value="<=">Minore Uguale</option>
            <option value="=">Uguale</option>
            <option value=">">Maggiore</option>
            <option value=">=">Maggiore Uguale</option>
        </select>
      </div>
      <div class="col-md-2">
          <div class="form-group">
            <label>&nbsp;</label>
            <input type="date" class="form-control" name="data_scadenza">
          </div>
        </div>
      <div class="form-group">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-block btn-primary">Ricerca</button>
      </div>
  </div>
  <div class="form-row">
    
  </div>
</form>

@if(!empty($msgSearch))
<div class="alert alert-warning alert-dismissible fade show" role="alert" >Ricerca effettuata:<strong> {{$msgSearch}}</strong>
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
</div>
@endif

@if(!empty($patenti))
<div id="results" class="alert alert-success"> Numero di patenti trovate: <strong> {{ $patenti->total() }} </strong></div>

<div class="table-responsive">
  <table class="table table-hover table-bordered table-sm"  style="table-layout:auto;overflow-x:scroll;">
    <thead class="thead-inverse">
        <tr>
            <th> Nominativo</th>
            <th> Numero Patente</th>
            <th> Categoria  </th>
            <th> Data Scadenza </th>
            <th> Operazioni </th>
        </tr>
    </thead>
    <tbody>
          @foreach($patenti as $patente)
          <tr hoverable>
              <td> {{$patente->persona->nominativo}}
              <span class="badge badge-warning">{{$patente->stato}}</span>
              </td>
              <td> {{$patente->numero_patente}}</td>
              <td>
                @foreach($patente->categorie as $categoria)
                {{$categoria->categoria}}&nbsp;&nbsp;
                @endforeach
              </td>
              <td> {{$patente->data_scadenza_patente}}</td>
              <td>
                <div class='btn-group' role='group' aria-label="Basic example">
                <a class="btn btn-warning" href="{{ route('patente.modifica', $patente->numero_patente) }}">Modifica</a>
                </div>
              </td>
          </tr>
          @endforeach
    </tbody>
  </table>
  <div class="col-md-2 offset-md-5">
    {{ $patenti->links("pagination::bootstrap-4") }}
  </div>
  </div>
  @endif
@endsection
