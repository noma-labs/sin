@extends('nomadelfia.index')



@section('archivio')

@include('partials.header', ['title' => 'Gestione Mamme Di Vocazione'])

<div class="row justify-content-md-center">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header"> 
              <h5>Mammae di vocazione <span class="badge badge-primary"> {{count($mvocazione)}}</span></h5>
            </div>
            <div class="card-body">
              <div class="row">
                <p class="col-md-3 font-weight-bold"> Nominativo</p>
                <p class="col-md-3 font-weight-bold"> Data inizio</p>
                <p class="col-md-3 font-weight-bold"> Durata </p>
                <p class="col-md-3 font-weight-bold"> Operazioni </p>
              </div>
      
              @forelse($mvocazione as $mamma)
            
                <div class="row">
                  <p class="col-md-3"> @include('nomadelfia.templates.persona', ['persona'=>$mamma])</p>
                  <p class="col-md-3">{{$mamma->data_inizio}}</p>
                  <p class="col-md-3"> @diffHumans($mamma->data_inizio)</p>
                  <div class="col-md-3"> 
                 
                  </div>
                </div>
      
              @empty
              <p class="text-danger">Nessun mamma di vocazione</p>
              @endforelse
            </div>  <!--end card body-->
      
          </div> <!--end card -->
    </div>
</div>
@endsection
