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
        @forelse($gruppi as $gruppo)
            <div class="row">
              <p class="col-md-3 font-weight-bold"> Gruppo familiare</p>
              <p class="col-md-2 font-weight-bold"> Data entrata</p>
              <p class="col-md-2 font-weight-bold"> Tempo trascorso </p>
              <p class="col-md-5 font-weight-bold"> Operazioni</p>
            </div>
            <div class="row">
              <p class="col-md-3">{{$gruppo->nome}}</p>
              <p class="col-md-2">{{$gruppo->pivot->data_entrata_gruppo }} </p>
              <div class="col-md-2">
                <span class="badge badge-info"> @diffHumans($gruppo->pivot->data_entrata_gruppo)</span>
               </div>
               <div class="col-md-5">
                <my-modal modal-title="Modifica Gruppo familiare attuale" button-title="Modifica" button-style="btn-warning my-2">
                  <template slot="modal-body-slot">
                    <form class="form" method="POST"  id="formPersonaGruppoModifica{{$gruppo->id}}" action="{{ route('nomadelfia.persone.gruppo.modifica', ['idPersona' =>$persona->id, 'id'=>$gruppo->id]) }}" >      
                        {{ csrf_field() }}
                        <input type="hidden" name="current_data_entrata"  value="{{$gruppo->pivot->data_entrata_gruppo }}"  />
                        <div class="form-group row">
                          <label for="staticEmail" class="col-sm-6 col-form-label">Gruppo familiare attuale</label>
                          <div class="col-sm-6">
                              <div>{{$gruppo->nome}}</div>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-sm-6 col-form-label">Data entrata</label>
                          <div class="col-sm-6">
                            <date-picker :bootstrap-styling="true" value="{{$gruppo->pivot->data_entrata_gruppo }}" format="yyyy-MM-dd" name="new_data_entrata"></date-picker>
                          </div>
                        </div>
                      </form>
                  </template> 
                  <template slot="modal-button">
                    <button class="btn btn-success" form="formPersonaGruppoModifica{{$gruppo->id}}" >Salva</button>
                  </template>
                </my-modal> <!--end modal modifica posizione-->
               
              
                    <!--modal concludi gruppo -->
              <my-modal modal-title="Concludi Gruppo familiare" button-title="Concludi" button-style="btn-info my-2">
                  <template slot="modal-body-slot">
                      <form class="form" method="POST"  id="formConcludigruppo{{$gruppo->id}}" action="{{ route('nomadelfia.persone.gruppo.concludi', ['idPersona' =>$persona->id, 'id'=>$gruppo->id]) }}" >      
                          {{ csrf_field() }}
                          <input type="hidden" name="data_entrata"  value="{{$gruppo->pivot->data_entrata_gruppo }}"  />
                          <div class="form-group row">
                            <label for="staticEmail" class="col-sm-6 col-form-label">Gruppo familiare attuale</label>
                            <div class="col-sm-6">
                                <div>{{$gruppo->nome}}</div>
                            </div>
                          </div>
                      
                          <div class="form-group row">
                            <label class="col-sm-6 col-form-label">Data uscita gruppo</label>
                            <div class="col-sm-6">
                              <date-picker :bootstrap-styling="true" value="{{$gruppo->pivot->data_uscita_gruppo }}" format="yyyy-MM-dd" name="data_uscita"></date-picker>
                            </div>
                          </div>

                        </form>
                  </template> 
                  <template slot="modal-button">
                    <button class="btn btn-success" form="formConcludigruppo{{$gruppo->id}}" >Salva</button>
                  </template>
              </my-modal> 

              @include('nomadelfia.templates.eliminaPersonaDalGruppo',['persona'=>$persona, 'gruppo'=>$gruppo])
              </div>
            </div>
          @empty
           <p class="text-danger">Nessun gruppo familiare</p>
          @endforelse
          <!--modal modifica gruppo attuale -->
          <my-modal modal-title="Aggiungi Gruppo Familiare" button-title="Nuovo Gruppo" button-style="btn-success my-2">
            <template slot="modal-body-slot">
            <form class="form" method="POST"  id="formPersonaGruppo" action="{{ route('nomadelfia.persone.gruppo.assegna', ['idPersona' =>$persona->id]) }}" >      
                {{ csrf_field() }}
             
                <h5 class="my-2">Nuovo gruppo familiare</h5>
                <div class="form-group row">
                  <label for="staticEmail" class="col-sm-6 col-form-label">Gruppo familiare</label>
                  <div class="col-sm-6">
                    <select name="gruppo_id" class="form-control">
                        <option selected>---seleziona gruppo ---</option>
                        @foreach (App\Nomadelfia\Models\GruppoFamiliare::all() as $gruppofam)
                          <option value="{{$gruppofam->id}}" {{ old('gruppo_id') == $gruppofam->id ? 'selected' : '' }}>{{$gruppofam->nome}}</option>
                        @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPassword" class="col-sm-6 col-form-label">Data entrata gruppo familiare</label>
                  <div class="col-sm-6">
                    <date-picker :bootstrap-styling="true" value="{{ old('data_entrata') }}" format="yyyy-MM-dd"name="data_entrata"></date-picker>
                  </div>
                </div>
              </form>
            </template> 
            <template slot="modal-button">
              <button class="btn btn-success" form="formPersonaGruppo">Salva</button>
            </template>
          </my-modal> <!--end modal-->
     
          
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
