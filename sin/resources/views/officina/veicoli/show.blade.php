@extends('officina.index')

@section('archivio')
@include('partials.header', ['title' => 'Dettaglio Veicolo'])
<div class="row">
  <div class="col-md-8">
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label>Targa</label>
          <input type="text" class="form-control"value="{{$veicolo->targa}}" disabled>
       </div>
     </div>
     <div class="col-md-3">
        <div class="form-group">
          <label for="nome">Nome</label>
          <input type="text" class="form-control" value="{{$veicolo->nome}}" disabled>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
           <label for="marca">Marca</label>
           <input type="text" class="form-control" value="{{$veicolo->modello->marca->nome}}" disabled>
         </div>
       </div>

      <div class="col-md-3">
        <div class="form-group">
           <label for="modello">Modello</label>
           <input type="text" class="form-control" value="{{$veicolo->modello->nome}}" disabled>
         </div>
       </div>
    </div>
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label for="tipologia" >Tipologia</label>
          <input type="text" class="form-control" value="{{$veicolo->tipologia->nome}}" disabled>
         </div>
       </div>
      <div class="col-md-3">
        <div class="form-group">
            <label for="impiego">Impiego</label>
            <input type="text" class="form-control" value="{{$veicolo->impiego->nome}}" disabled>
           </div>
         </div>

       <div class="col-md-3">
         <div class="form-group">
           <label for="alimentazione">Alimentazione</label>
           <input type="text" class="form-control" value="{{$veicolo->alimentazione->nome}}" disabled>
         </div>
       </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="posti">N. Posti</label>
            <input type="text" class="form-control" value="{{$veicolo->num_posti}}" disabled>
           </div>
         </div>

      </div>
  </div>
  <div class="col-md-4">
    <div class="card" >
      <!-- <img class="card-img-top" src="..." alt="Card image cap"> -->
      <div class="card-header">
        <h3 class="card-title">Ultime Manutenzioni</h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover table-bordered table-sm" >
          <thead class="thead-inverse">
            <tr>
              <th width="20%">Data</th>
              <th width="30%">Tipo Manutenzione</th>
              <th width="20%">Km</th>
              <th width="30%">Meccanico</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>22-07-2019</td>
              <td>Giro Gomme</td>
              <td>85.000</td>
              <td>Daniele S.</td>
            </tr>
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </div>
</div>

  <a class="btn btn-primary" href="{{route('veicoli.modifica',['id' => $veicolo->id])}}">Modifica</a>


<!-- end section dettagli prenotazione -->
@endsection
