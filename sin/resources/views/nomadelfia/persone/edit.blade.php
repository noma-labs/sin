@extends('biblioteca.libri.index')

@section('archivio')

@include('partials.header', ['title' => 'Modifica Cliente'])

<form class="form" method="POST" action="{{ route('clienti.modifica', ['idCliente' =>$cliente->ID_CLIENTE]) }}" >
{{ csrf_field() }}
<div class="row">
  <div class="col-md-6">
    <div class="row">
      <div class="col-md-6">
        <label for="xNominativo">NOMINATIVO</label>
        <input type="text" class="form-control" name="xNominativo" value="{{$cliente->nominativo}}">
     </div>
     </div>
    <div class="row">
     <div class=" col-md-6">
        <label for="xNome">NOME</label>
        <input type="text" class="form-control" name="xNome" value="{{$cliente->NOME}}" >
      </div>
      </div>
    <div class="row">
      <div class="col-md-6">
         <label for="xCognome">COGNOME</label>
         <input type="text" class="form-control" name="xCognome"  value="{{$cliente->COGNOME}}" >
       </div>
    </div>
    <!-- End first row -->


    <!-- @include('partials.errors') -->

    <div class="row">
     <div class="col-md-12">
       <div class="btn-toolbar pull-left">
          <button class="btn btn-success"  type="submit">Salva Modifiche</button>
      </div>
       <div class="btn-toolbar pull-right">
         <button class="btn btn-danger"   type="submit">Elimina Cliente</button>
        </div>
    </div>
    </div>

       </div>
    <!-- End buttons  row -->

  </div>
  <!-- end column book info -->

  <div class="col-md-6">
  </div>
  </div>
<!-- end section dettagli prenotazione -->
</form>

@endsection
