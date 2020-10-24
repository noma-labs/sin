@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => "Gestione Posizione  ".$persona->nominativo])

<div class="row justify-content-center">
   <div class="col-md-6">
  
    <!-- card posizione attuale -->
    <div class="card">
      <div class="card-header">
        Posizione attuale
      </div>
      <div class="card-body">
          @if($attuale)
            <div class="row">
              <p class="col-md-3 font-weight-bold"> Posizione</p>
              <p class="col-md-2 font-weight-bold"> Data Inizio</p>
              <p class="col-md-2 font-weight-bold"> Tempo trascorso </p>
              <p class="col-md-5 font-weight-bold"> Operazioni</p>
            </div>
            <div class="row">
              <p class="col-md-3"> {{$attuale->nome}}</p>
              <p class="col-md-2">{{$attuale->pivot->data_inizio }} </p>
              <div class="col-md-2">
                <span class="badge badge-info"> @diffHumans($attuale->pivot->data_inizio) </span>
               </div>
              <div class="col-md-5">
                <my-modal modal-title="Modifica Posizione attuale" button-title="Modifica" button-style="btn-warning my-2">
                  <template slot="modal-body-slot">
                  <form class="form" method="POST"  id="formPersonaPosizioneModifica{{$attuale->id}}" action="{{ route('nomadelfia.persone.posizione.modifica', ['idPersona' =>$persona->id, 'id'=>$attuale->id]) }}" >      
                        {{ csrf_field() }}
                        <input type="hidden" name="current_data_inizio"  value="{{$attuale->pivot->data_inizio }}"  />
                        <div class="form-group row">
                          <label for="staticEmail" class="col-sm-6 col-form-label">Posizione attuale</label>
                          <div class="col-sm-6">
                              <div>{{$attuale->nome}}</div>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-6 col-form-label">Data inizio</label>
                          <div class="col-sm-6">
                            <date-picker :bootstrap-styling="true" value="{{$attuale->pivot->data_inizio }}" format="yyyy-MM-dd" name="new_data_inizio"></date-picker>
                          </div>
                        </div>
                      </form>
                  </template> 

                  <template slot="modal-button">
                    <button class="btn btn-success" form="formPersonaPosizioneModifica{{$attuale->id}}">Salva</button>
                  </template>
                </my-modal> <!--end modal modifica posizione-->
                               
                <my-modal modal-title="Concludi Posizione" button-title="Concludi" button-style="btn-info my-2">
                    <template slot="modal-body-slot">
                        <form class="form" method="POST"  id="formConcludiPosizione{{$attuale->id}}" action="{{ route('nomadelfia.persone.posizione.concludi', ['idPersona' =>$persona->id, 'id'=>$attuale->id]) }}" >      
                            {{ csrf_field() }}
                            @method('PUT')
                            <input type="hidden" name="data_inizio"  value="{{$attuale->pivot->data_inizio }}"  />
                            <div class="form-group row">
                              <label for="staticEmail" class="col-sm-6 col-form-label">posizione familiare attuale</label>
                              <div class="col-sm-6">
                                  <div>{{$attuale->nome}}</div>
                              </div>
                            </div>
                        
                            <div class="form-group row">
                              <label class="col-sm-6 col-form-label">Data fine posizione</label>
                              <div class="col-sm-6">
                                <date-picker :bootstrap-styling="true" value="{{$attuale->pivot->data_fine }}" format="yyyy-MM-dd" name="data_fine"></date-picker>
                              </div>
                            </div>
  
                          </form>
                    </template> 
                    <template slot="modal-button">
                      <button class="btn btn-success" form="formConcludiPosizione{{$attuale->id}}" >Salva</button>
                    </template>
                </my-modal> 

                @include('nomadelfia.templates.eliminaPersonaPosizione',['persona'=>$persona, 'posizione'=>$attuale])
               </div>
            </div>
          @else
           <p class="text-danger">Nessuna posizione</p>
          @endif
          <my-modal modal-title="Aggiungi Posizione persona" button-title="Nuova Posizione" button-style="btn-success  my-2">
              <template slot="modal-body-slot">
                <form class="form" method="POST"  id="formPersonaPosizione" action="{{ route('nomadelfia.persone.posizione.assegna', ['idPersona' =>$persona->id]) }}" >      
                    {{ csrf_field() }}
                    @if($attuale)
                    <h5 class="my-2">Completa dati della posizione attuale: {{$attuale->nome}}</h5>
                    <div class="form-group row">
                      <label for="inputPassword" class="col-sm-6 col-form-label">Data fine posizione</label>
                      <div class="col-sm-6">
                        <date-picker :bootstrap-styling="true" value="{{ Carbon::now()->toDateString()}}" format="yyyy-MM-dd" name="data_fine"></date-picker>
                        <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se concide con la data di inizio della nuova posizione .</small>
                      </div>
                    </div>
                    <hr>
                    @endif
                    <h5 class="my-2">Inserimento nuova posizione</h5>
                    <div class="form-group row">
                      <label for="staticEmail" class="col-sm-6 col-form-label">Posizione</label>
                      <div class="col-sm-6">
                        <select name="posizione_id" class="form-control">
                            <option value="" selected>---seleziona posizione---</option>
                            @foreach ($persona->posizioniPossibili() as $posizione)
                              <option value="{{$posizione->id}}">{{$posizione->nome}}</option>
                            @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-sm-6 col-form-label">Data inizio</label>
                      <div class="col-sm-6">
                        <!-- <input type="date" name="data_inizio" class="form-control" id="inputPassword" placeholder="Password"> -->
                        <date-picker :bootstrap-styling="true" value="{{ old('data_inizio')? old('data_inizio'): Carbon::now()->toDateString()}}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
                      </div>
                    </div>
                </form>
              </template> 
              <template slot="modal-button">
                <button class="btn btn-success" form="formPersonaPosizione">Salva</button>
              </template>
            </my-modal> <!--end modal aggiungi posizione-->
      </div>  <!--end card body-->
    </div> <!--end card -->
     
     <!-- card posizione storico -->
    <div class="card my-3">
      <div class="card-header">
       Storico delle Posizione 
      </div>
      <div class="card-body">
        <div class="row">
          <p class="col-md-2 font-weight-bold"> Posizione</p>
          <p class="col-md-2 font-weight-bold"> Data inizio</p>
          <p class="col-md-2 font-weight-bold"> Data fine </p>
          <p class="col-md-2 font-weight-bold"> Durata  </p>
          <p class="col-md-4 font-weight-bold"> Operazioni  </p>
        </div>

        @forelse($storico as $posizionestor)
      
        <div class="row">
          <p class="col-md-2"> {{$posizionestor->nome}}</p>
          <p class="col-md-2">{{$posizionestor->pivot->data_inizio }} </p>
          <p class="col-md-2">{{$posizionestor->pivot->data_fine }} </p>

          <div class="col-md-2">
            <span class="badge badge-info"> 
            {{Carbon::parse($posizionestor->pivot->data_fine)->diffForHumans(Carbon::parse($posizionestor->pivot->data_inizio),['short' => true])}}</span>
            </div>
            <div class="col-md-2">@include('nomadelfia.templates.eliminaPersonaPosizione',['persona'=>$persona, 'posizione'=>$posizionestor])</div>
            
        </div>

        @empty
        <p class="text-danger">Nessuna posizione nello storico</p>
        @endforelse
      </div>  <!--end card body-->
     </div> <!--end card -->

   </div> <!--end card -->
 </div> <!--end col -md-6 -->
</div> <!--end row justify-->

@endsection
