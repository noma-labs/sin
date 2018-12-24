@extends('officina.index')
@section('title', 'Officina patenti')

@section('archivio')
<div class="my-page-title">
    <div class="d-flex justify-content-end">
    <div class="mr-auto p-2"><span class="h1 text-center">Visualizazioni Patente </span></div>
    <div class="p-2 text-right">
      <h5 class="m-1">
        ## patenti
      </h5 >
    </div>
  </div>
</div>

@include('patente.elenchi.percategoria')

@endsection
