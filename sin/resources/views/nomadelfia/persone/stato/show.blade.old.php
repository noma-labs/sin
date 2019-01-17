@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => "Gestione stato  ".$persona->nominativo])

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
              <div class="row">
                <h5 class="col-md-4">Stato attuale:</h5>
                <div class="col-md-6">
                    @if($persona->statoAttuale())
                      {{$persona->statoAttuale()->nome}}
                    @else
                      <p class="text-danger">Nessuno stato</p>
                    @endif
                </div>
                <div class="col-md-2">
                  <my-modal modal-title="Aggiungi Stato persona" button-title="Aggiungi">
                          <template slot="modal-body-slot">
                          <form class="form" method="POST"  id="formStatoPersona" action="{{ route('nomadelfia.persone.stato.assegna', ['idPersona' =>$persona->id]) }}" >      
                              {{ csrf_field() }}

                             @if($persona->statoAttuale())
                             <p> Inserisci informazioni sullo stato attuale: </p>
                              <div class="form-group row">
                                <label for="inputPassword" class="col-sm-6 col-form-label">Data fine  {{$persona->statoAttuale()->nome}}</label>
                                <div class="col-sm-6">
                                  <input type="date" name="data_inizio" class="form-control" id="inputPassword" placeholder="Password">
                                </div>
                              </div>
                              <hr>
                              @endif
                              <p>Insertisci il nuovo stato:</p>
                              <div class="form-group row">
                                <label for="staticEmail" class="col-sm-4 col-form-label">Stato</label>
                                <div class="col-sm-8">
                                  <select name="stato_id" class="form-control">
                                      <option selecte>---seleziona stato---</option>
                                      @foreach (App\Nomadelfia\Models\Stato::all() as $stato)
                                        <option value="{{$stato->id}}">{{$stato->nome}}</option>
                                    @endforeach
                                  </select>
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="inputPassword" class="col-sm-4 col-form-label">Data inizio</label>
                                <div class="col-sm-8">
                                  <input type="date" name="data_inizio" class="form-control" id="inputPassword" placeholder="Password">
                                </div>
                              </div>
                            
                            
                            </form>
                          </template> 
                          <template slot="modal-button">
                            <button class="btn btn-success" form="formStatoPersona">Salva</button>
                          </template>
                        </my-modal>
                </div>
              </div>

            <h5 class="my-2">Storico degli stati assegnati alla persona:</h5>
          
              <div class="row">
                <label class="col-sm-4">Stato</label>
                <label class="col-sm-6"> Data inizio  </label>
                <label class="col-sm-2"> Data fine</label>
              </div>
            <ul>
            @forelse($persona->statiStorico as $statostor)
            <li>
              <div class="row">
                <label class="col-sm-4"> {{$statostor->nome}}</label>
                <div class="col-sm-6">
                     {{$statostor->pivot->data_inizio}} 
                </div>
                <div class="col-sm-2">
                     {{$statostor->pivot->data_fine}}
                </div>
              </div>
            </li>
            @empty
            <p class="text-danger">Nessuno stato</p>
            @endforelse
          </ul>
      </div>
    </div>
  </div>
  </div>
</div>

@endsection
