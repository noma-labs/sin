@extends('officina.index')
@section('title', 'Veicoli')

@section('archivio')
<div class="my-page-title">
    <div class="d-flex justify-content-end">
    <div class="mr-auto p-2"><span class="h1 text-center">Gestione Veicoli </span></div>
    <div class="p-2 text-right">
      <h5 class="m-1">
        {{App\Officina\Models\Veicolo::count()}} veicoli
      </h5 >
    </div>
  </div>
</div>

<div class="card card-mod" >
    <div class="card-header card-header-mod">
        <h3>Ricerca</h3>
    </div>
    <div class="card-body card-body-mod">
        <form action="" method="get" class="form-inline">
          <label for="nome" class="sr-only">Nome</label>
          <input type="text" name="nome" id="nome" class="form-control" placeholder="Nome">
          
          <label for="targa" class="sr-only">Targa</label>
          <input type="text" name="targa" id="targa" class="form-control ml-3" placeholder="Targa">

          <label for="marca" class="sr-only">Marca</label>
          <select name="marca" id="marca" class="form-control ml-3">  
            <option value="">--Marche--</option>
            @foreach ($marche as $marca)
                <option value="{{$marca->id}}">{{$marca->nome}}</option>
            @endforeach
          </select>

          <label for="modello" class="sr-only">Modello</label>
          <select name="modello" id="modello" class="form-control ml-3">
            <option value="">--Modelli--</option>
            @foreach ($modelli as $modello)
                <option value="{{$modello->id}}">{{$modello->nome}}</option>
            @endforeach
          </select>

          <button class="btn btn-primary ml-3" type="submit">Cerca</button>
      </form>        
    </div>
  </div>

<div class="table-responsive">
  <table class='table table-hover table-bordered' style="overflow-x:scroll;table-layout:auto;">
    <thead class="thead-inverse">
      <tr>
        <th width="2%">#</th>
        <th width="20%">Nome</th>
        <th width="12%">Targa</th>
        <th width="9%">Marca</th>
        <th width="9%">Modello</th>
        <th width="11%">Impiego</th>
        <th width="8%">Tipologia</th>
        <th width="8%">Alimentazione</th>
        <th width="5%">Posti</th>
        <th width="16%">Operazioni</th>
      </tr>
    </thead>
    <tbody>
      @foreach ( $veicoli as $veicolo)
      <tr hoverable>
        <td>{{$loop->iteration}}</td>
        <td>{{ $veicolo->nome }}
          @if ($veicolo->prenotabile)
          <span  class="badge badge-success">prenotabile</span>
         @endif
        </td>
        <td>{{ $veicolo->targa }}</td>
        <td>{{ $veicolo->modello->marca->nome }}</td>
        <td>{{ $veicolo->modello->nome }}</td>
        <td>{{ $veicolo->impiego->nome }}</td>
        <td>{{ $veicolo->tipologia->nome }}</td>
        <td>{{ $veicolo->alimentazione->nome }}</td>
        <td>{{ $veicolo->num_posti }}</td>
        <td>
          <a class="btn btn-warning btn-sm" href="{{route('veicoli.dettaglio',['id' => $veicolo->id])}}">Dettagli</a>
          <a class="btn btn-success btn-sm" href="{{route('veicoli.modifica',['id' => $veicolo->id])}}">Modifica</a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
