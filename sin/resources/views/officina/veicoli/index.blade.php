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

<div class="table-responsive">
  <table class='table table-hover table-bordered' style="overflow-x:scroll;table-layout:auto;">
    <thead class="thead-inverse">
      <tr>
        <th>Nome</th>
        <th>Targa</th>
        <th>Marca</th>
        <th>Modello</th>
        <th>Impiego</th>
        <th>Tipologia</th>
        <th>Alimentazione</th>
        <th>Posti</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ( $veicoli as $veicolo)
      <tr hoverable>
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
          <a class="btn btn-warning" href="{{route('veicoli.dettaglio',['id' => $veicolo->id])}}">Dettagli</a>
          <a class="btn btn-success" href="{{route('veicoli.modifica',['id' => $veicolo->id])}}">Modifica</a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
