@extends('nomadelfia.index')



@section('archivio')

@include('partials.header', ['title' => 'Gestione Figli Maggiorenni'])

<div class="row justify-content-md-center">
    <div class="col-md-12">
      <div class="card my-2">
        <div class="card-body">
          <h3>Figli Maggiorenni <span class="badge badge-primary"> {{$maggiorenni->total}}</span></h3>
         </div> 
       </div> 
    </div> 
</div>

<div class="row justify-content-center">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
              <h5>Uomini <span class="badge badge-primary"> {{count($maggiorenni->uomini)}}</span></h5>
            </div>
            <div class="card-body">
              <div class="row">
                <p class="col-md-3 font-weight-bold"> Nominativo</p>
                <p class="col-md-3 font-weight-bold"> Data inizio</p>
                <p class="col-md-3 font-weight-bold"> Durata </p>
                <p class="col-md-3 font-weight-bold"> Operazioni </p>
              </div>
      
              @forelse($maggiorenni->uomini as $persona)
            
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
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
              <h5>Donne <span class="badge badge-primary"> {{count($maggiorenni->donne)}}</span></h5>
            </div>
            <div class="card-body">
              <div class="row">
                <p class="col-md-3 font-weight-bold"> Nominativo</p>
                <p class="col-md-3 font-weight-bold"> Data inizio</p>
                <p class="col-md-3 font-weight-bold"> Durata </p>
                <p class="col-md-3 font-weight-bold"> Operazioni </p>
              </div>
      
              @forelse($maggiorenni->donne as $persona)
            
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
@endsection
