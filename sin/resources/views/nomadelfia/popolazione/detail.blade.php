@extends('nomadelfia.index')

@section('title', 'Dettaglio Per posziione')


@section('archivio')

<div class="row justify-content-center">
  <div class="col-md-6">
     <div class="card">
       <div class="card-header">
         Posizione XXXXX
       </div>
       <div class="card-body">
         <div class="row">
           <p class="col-md-3 font-weight-bold"> Categoria</p>
           <p class="col-md-3 font-weight-bold"> Data inizio</p>
           <p class="col-md-3 font-weight-bold"> Data fine </p>
           <p class="col-md-3 font-weight-bold"> Durata  </p>
         </div>
 
         @forelse($persone as $persona)
       
          <div class="row">
            <p class="col-md-3"> {{$persona->nominativo}}</p>
            <p class="col-md-3"></p>
            <p class="col-md-3"></p>
            <div class="col-md-3"></div>
          </div>
 
         @empty
         <p class="text-danger">Nessuna categoria nello storico</p>
         @endforelse
       </div>  <!--end card body-->
      </div> <!--end card -->
 
    </div> <!--end card -->
  </div> <!--end col -md-6 -->
 </div> <!--end row justify-->
@endsection
