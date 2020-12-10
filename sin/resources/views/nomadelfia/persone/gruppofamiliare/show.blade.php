@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => "Gestione Gruppo Familiare  ".$persona->nominativo])

<div class="row justify-content-center">
   <div class="col-md-6">
 

    <div class="card">
      <div class="card-header">
        Gruppo Familiare attuale
      </div>
      <div class="card-body">
        @if($attuale)
            <div class="row">
              <p class="col-md-3 font-weight-bold"> Gruppo familiare</p>
              <p class="col-md-2 font-weight-bold"> Data entrata</p>
              <p class="col-md-2 font-weight-bold"> Tempo trascorso </p>
              <p class="col-md-5 font-weight-bold"> Operazioni</p>
            </div>
            <div class="row">
              <p class="col-md-3">{{$attuale->nome}}</p>
              <p class="col-md-2">{{$attuale->pivot->data_entrata_gruppo }} </p>
              <div class="col-md-2">
                <span class="badge badge-info"> @diffHumans($attuale->pivot->data_entrata_gruppo)</span>
               </div>
               <div class="col-md-5">
                <my-modal modal-title="Modifica Gruppo familiare attuale" button-title="Modifica" button-style="btn-warning my-2">
                  <template slot="modal-body-slot">
                    <form class="form" method="POST"  id="formPersonaGruppoModifica{{$attuale->id}}" action="{{ route('nomadelfia.persone.gruppo.modifica', ['idPersona' =>$persona->id, 'id'=>$attuale->id]) }}" >      
                        {{ csrf_field() }}
                        <input type="hidden" name="current_data_entrata"  value="{{$attuale->pivot->data_entrata_gruppo }}"  />
                        <div class="form-group row">
                          <label for="staticEmail" class="col-sm-6 col-form-label">Gruppo familiare attuale</label>
                          <div class="col-sm-6">
                              <div>{{$attuale->nome}}</div>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-6 col-form-label">Data entrata</label>
                          <div class="col-sm-6">
                            <date-picker :bootstrap-styling="true" value="{{$attuale->pivot->data_entrata_gruppo }}" format="yyyy-MM-dd" name="new_data_entrata"></date-picker>
                          </div>
                        </div>
                      </form>
                  </template> 
                  <template slot="modal-button">
                    <button class="btn btn-success" form="formPersonaGruppoModifica{{$attuale->id}}" >Salva</button>
                  </template>
                </my-modal> <!--end modal modifica posizione-->
               
              
                
              <my-modal modal-title="Concludi Gruppo familiare" button-title="Concludi" button-style="btn-info my-2">
                  <template slot="modal-body-slot">
                      <form class="form" method="POST"  id="formConcludigruppo{{$attuale->id}}" action="{{ route('nomadelfia.persone.gruppo.concludi', ['idPersona' =>$persona->id, 'id'=>$attuale->id]) }}" >      
                          {{ csrf_field() }}
                          <input type="hidden" name="data_entrata"  value="{{$attuale->pivot->data_entrata_gruppo }}"  />
                          <div class="form-group row">
                            <label for="staticEmail" class="col-sm-6 col-form-label">Gruppo familiare attuale</label>
                            <div class="col-sm-6">
                                <div>{{$attuale->nome}}</div>
                            </div>
                          </div>
                      
                          <div class="form-group row">
                            <label class="col-sm-6 col-form-label">Data uscita gruppo</label>
                            <div class="col-sm-6">
                              <date-picker :bootstrap-styling="true" value="{{$attuale->pivot->data_uscita_gruppo }}" format="yyyy-MM-dd" name="data_uscita"></date-picker>
                            </div>
                          </div>

                        </form>
                  </template> 
                  <template slot="modal-button">
                    <button class="btn btn-success" form="formConcludigruppo{{$attuale->id}}" >Salva</button>
                  </template>
              </my-modal> 

              @include('nomadelfia.templates.eliminaPersonaDalGruppo',['persona'=>$persona, 'gruppo'=>$attuale])
              </div>
            </div>
          @else 
             <p class="text-danger">Nessun gruppo familiare</p>
          @endif

          @if ($attuale)
             @include("nomadelfia.templates.spostaPersonaGruppo",['persona' => $persona, 'attuale'=>$attuale])
          @else
             @include("nomadelfia.templates.assegnaPersonaNuovoGruppo",['persona' => $persona])
          @endif        
        
          
        </div>  <!--end card body-->
    </div> <!--end card -->
    <div class="card my-3">
      <div class="card-header">
       Storico dei gruppi familiari
      </div>
      <div class="card-body">
        <div class="row">
          <p class="col-md-3 font-weight-bold"> Gruppo</p>
          <p class="col-md-2 font-weight-bold"> Data inizio</p>
          <p class="col-md-2 font-weight-bold"> Data fine </p>
          <p class="col-md-2 font-weight-bold"> Tempo trascorso</p>
          <p class="col-md-3 font-weight-bold"> Operazioni</p>
        </div>
        @forelse($persona->gruppofamiliariStorico as $gruppostorico)
        <div class="row">
          <p class="col-md-3"> {{$gruppostorico->nome}}</p>
          <p class="col-md-2">{{$gruppostorico->pivot->data_entrata_gruppo }} </p>
          <p class="col-md-2">{{$gruppostorico->pivot->data_uscita_gruppo }} </p>
          <div class="col-md-2">
            <span class="badge badge-info"> 
            {{Carbon::parse($gruppostorico->pivot->data_uscita_gruppo)->diffForHumans(Carbon::parse($gruppostorico->pivot->data_entrata_gruppo),['short' => true])}}</span>
          </div>
          <div class="col-md-3">
           @include('nomadelfia.templates.eliminaPersonaDalGruppo',['persona'=>$persona, 'gruppo'=>$gruppostorico])
          </div>
        </div>

        @empty
        <p class="text-danger">Nessuna storico</p>
        @endforelse

      </div>  <!--end card body-->
     </div> <!--end card -->

   </div> <!--end card -->
 </div> <!--end col -md-6 -->
</div> <!--end row justify-->

@endsection
