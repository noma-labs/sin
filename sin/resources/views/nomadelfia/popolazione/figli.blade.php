@extends('nomadelfia.index')

@section('title', 'Gestione Figli')


@section('archivio')

<div class="row justify-content-center">
    <div class="col-sm-9">
        Figli <span class="badge badge-primary"> {{$count}}</span>
      <div class="row">
        <div class="col-8 col-sm-6">
            <div class="card">
                <div class="card-header">
                  Figli Uomini <span class="badge badge-primary">  {{count($uomini)}} </span>
                </div>
                <div class="card-body">
                  <div class="row">
                    <p class="col-md-3 font-weight-bold"> Nominativo</p>
                    <p class="col-md-3 font-weight-bold"> Data inizio</p>
                    <p class="col-md-3 font-weight-bold"> Durata </p>
                    <p class="col-md-3 font-weight-bold"> Operazioni </p>
                  </div>
          
                  @forelse($uomini as $persona)
                
                   <div class="row">
                     <p class="col-md-3"> @include('nomadelfia.templates.persona', ['persona'=>$persona])</p>
                     <p class="col-md-3">{{$persona->data_inizio}}</p>
                     <p class="col-md-3"> @diffHumans($persona->data_inizio)</p>
                     <div class="col-md-3"> 
                        @include("nomadelfia.templates.modificaDataPosizione",["persona"=>$persona, 'id'=>$persona->posizione_id, "data_inizio"=>$persona->data_inizio])
                     </div>
                   </div>
          
                  @empty
                  <p class="text-danger">Nessuna categoria nello storico</p>
                  @endforelse
                </div>  <!--end card body-->
          
             </div> <!--end card -->
        </div>
        <div class="col-4 col-sm-6">
            <div class="card">
                <div class="card-header">
                  Figli Donne <span class="badge badge-primary">  {{count($donne)}} </span>
                </div>
                <div class="card-body">
                  <div class="row">
                    <p class="col-md-3 font-weight-bold"> Nominativo</p>
                    <p class="col-md-3 font-weight-bold"> Data inizio</p>
                    <p class="col-md-3 font-weight-bold"> Durata </p>
                    <p class="col-md-3 font-weight-bold"> Operazioni </p>
                  </div>
          
                  @forelse($donne as $persona)
                
                   <div class="row">
                     <p class="col-md-3"> @include('nomadelfia.templates.persona', ['persona'=>$persona])</p>
                     <p class="col-md-3">{{$persona->data_inizio}}</p>
                     <p class="col-md-3"> @diffHumans($persona->data_inizio)</p>
                     <div class="col-md-3"> 
                        @include("nomadelfia.templates.modificaDataPosizione",["persona"=>$persona, 'id'=>$persona->posizione_id, "data_inizio"=>$persona->data_inizio])
                     </div>
                   </div>
          
                  @empty
                  <p class="text-danger">Nessuna categoria nello storico</p>
                  @endforelse
                </div>  <!--end card body-->
          
             </div> <!--end card -->
        </div>
      </div>
    </div>
  </div>
@endsection
