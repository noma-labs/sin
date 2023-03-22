@extends('nomadelfia.index')

@section('archivio')
@include('partials.header', ['title' => 'Gestione Esercizi Spirituale'])

<div class="row justify-content-md-center">
    <div class="col-md-12">
      <div class="card border-dark my-2">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Es. Spirituale:</label>
                        <div class="col-sm-6"> {{$esercizio->turno}} </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Responsabile:</label>
                        <div class="col-sm-6">
                            @if ($esercizio->responsabile)
                                <span class="text-bold">{{$esercizio->responsabile->nominativo}} </span> 
                            @else
                                <span class="text-danger">Responsabile non assegnato</span> 
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Persone:</label>
                        <div class="col-sm-3">
                            <span class="badge badge-info">{{$persone->total}}</span>
                        </div>
                    </div>
                </div> 
                <div class="col-md-6">
                 <div class="row">
                        <label class="col-sm-6 font-weight-bold">Data inzio:</label>
                        <div class="col-sm-6">{{$esercizio->data_inizio}} </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Data fine:</label>
                        <div class="col-sm-6">{{$esercizio->data_fine}} </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Luogo:</label>
                        <div class="col-sm-6">{{$esercizio->luogo}} </div>
                    </div>
                </div> 
            </div>          
         </div> 
       </div> 
    </div> 
</div>

<div class="col-md-3">
    @include("nomadelfia.templates.assegnaEsSpirituale",["esercizio"=>$esercizio ])
</div>

<div class="row justify-content-center">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
              <h5>Uomini <span class="badge badge-primary"> {{count($persone->uomini)}}</span></h5>
            </div>
            <div class="card-body">
              <div class="row">
                <p class="col-md-6  font-weight-bold"> Nominativo</p>
                <p class="col-md-6  font-weight-bold"> Operazioni </p>
              </div>
      
              @forelse($persone->uomini as $persona)
                <div class="row">
                  <p class="col-md-6"> @include('nomadelfia.templates.persona', ['persona'=>$persona])</p>
                  <div class="col-md-6"> 
                   @include("nomadelfia.templates.eliminaPersonaPosizione",["esercizio"=>$esercizio, 'persona'=>$persona])
                  </div>
                </div>
      
              @empty
              <p class="text-danger">Nessun maggiorenne</p>
              @endforelse
            </div>  <!--end card body-->
      
          </div> <!--end card -->
    </div>
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
              <h5>Donne <span class="badge badge-primary"> {{count($persone->donne)}}</span></h5>
            </div>
            <div class="card-body">
              <div class="row">
                <p class="col-md-6 font-weight-bold"> Nominativo</p>
                <p class="col-md-6 font-weight-bold"> Operazioni</p>
              </div>
      
              @forelse($persone->donne as $persona)
                <div class="row">
                  <p class="col-md-6"> @include('nomadelfia.templates.persona', ['persona'=>$persona])</p>
                  <div class="col-md-6"> 
                   @include("nomadelfia.templates.eliminaPersonaPosizione",["esercizio"=>$esercizio, 'persona'=>$persona])
                  </div>
                </div>
              @empty
                 <p class="text-danger">Nessun maggiorenne</p>
              @endforelse
            </div> 
          </div> 
    </div>
</div>

@endsection


