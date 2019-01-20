@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Famiglia'])

<!-- <div class="container"> -->
<!-- <div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
       <div class="row">
          <label class="col-md-4">Nome famiglia:</label> 
          <div class="col-md-4">
              <span>{{$famiglia->nome_famiglia}}</span>
          </div>
        </div>
        
        <div class="row">
          <label class="col-md-4">Stato famiglia:</label> 
          <div class="col-md-4">
              @if($famiglia->stato == '1')
                <a href="#" class="badge badge-success">Attivo</a>
              @else
              <a href="#" class="badge badge-danger">Disattivo</a>
              @endif
          </div>
          <div class="col-md-4">
            <my-modal modal-title="Modifica stato persona" button-title="Modifica">
              <template slot="modal-body-slot">
              <form class="form" method="POST"  id="formStato" >      
                  {{ csrf_field() }}
                </form>
              </template> 
              <template slot="modal-button">
                <button class="btn btn-success" form="formStato">Salva</button>
              </template>
            </my-modal>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div> -->

<div class="row my-3">
<div class="col-md-8 mb-2"> <!--  start col dati anagrafici -->
    <div class="card">
      <div class="card-header" id="headingOne">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
           Nome Famiglia: {{$famiglia->nome_famiglia}}
          </button>
        </h5>
      </div>
      <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
        <div class="card-body">

           @include("nomadelfia.templates.famigliaStorico", ['famiglia' => $famiglia])

          <my-modal modal-title="Aggiungi componente alla famiglia" button-title="Aggiungi Componente" button-style="btn-primary my-2">
            <template slot="modal-body-slot">
              <form class="form" method="POST" id="formComponente" action="{{ route('nomadelfia.famiglie.componente.assegna', ['id' =>$famiglia->id]) }}" >      
                {{ csrf_field() }}
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Persona</label>
                    <div class="col-8">
                      <autocomplete placeholder="Inserisci nominativo..." name="persona_id" url="{{route('api.nomadeflia.persone.search')}}"></autocomplete>
                    </div>
                </div>
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Posizione Famiglia</label>
                    <div class="col-8">
                      <select class="form-control" name="posizione">
                      <option value="" selected>---scegli posizione---</option>
                        @foreach (App\Nomadelfia\Models\Famiglia::getEnum('Posizione') as $posizione)
                            @if($famiglia->capofamiglia() != null) -->
                            <option value="{{ $posizione }}">{{ $posizione }}</option>
                             @endif
                          @endforeach
                       </select>
                    </div>
                </div>
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Data entrata nella famiglia:</label>
                    <div class="col-8">
                      <input type="date" class="form-control" name="data_entrata"  placeholder="Data entrata nella famiglia" >
                    </div>
                </div>
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Stato:</label>
                    <div class="col-8">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="stato" id="stato1" value="1" checked>
                      <label class="form-check-label" for="stato1">
                        Includi nel nucleo familiare
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="stato" id="stato2" value="0">
                      <label class="form-check-label" for="stato2">
                        Non includere nel nucleo familiare
                      </label>
                    </div>
                    </div>
                </div>
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Note:</label>
                    <div class="col-8">
                      <!-- <input type="date" class="form-control" name="note" placeholder="Data entrata nella famiglia" > -->
                      <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>
                </div>
              </form>
            </template> 
            <template slot="modal-button">
                  <button class="btn btn-danger" form="formComponente">Salva</button>
            </template>
          </my-modal>

           <!-- <my-modal modal-title="Aggiorna componente" button-title="Aggiorna Componente" button-style="btn-success my-2">
            <template slot="modal-body-slot">
              <form class="form" method="POST" id="formComponenteAggiorna" action="{{ route('nomadelfia.famiglie.componente.aggiorna', ['id' =>$famiglia->id]) }}" >      
                {{ csrf_field() }}
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Persona</label>
                    <div class="col-8">
                    <select class="form-control" name="persona_id">
                      <option value="" selected>---scegli persona---</option>
                          @foreach($famiglia->componenti as $comp)
                            <option value="{{ $comp->id }}">{{ $comp->nominativo }}</option>
                          @endforeach
                       </select>
                    </div>
                </div>
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Posizione Famiglia</label>
                    <div class="col-8">
                      <select class="form-control" name="posizione">
                      <option value="" selected>---scegli posizione---</option>
                        @foreach (App\Nomadelfia\Models\Famiglia::getEnum('Posizione') as $posizione)
                            @if($posizione != "SINGLE" and $posizione != "CAPO FAMIGLIA")
                            <option value="{{ $posizione }}">{{ $posizione }}</option>
                            @endif
                          @endforeach
                       </select>
                    </div>
                </div>
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Data entrata nella famiglia:</label>
                    <div class="col-8">
                      <input type="date" class="form-control" name="data_entrata"  placeholder="Data entrata nella famiglia" >
                    </div>
                </div>
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Data uscita dalla famiglia:</label>
                    <div class="col-8">
                      <input type="date" class="form-control" name="data_uscita"  placeholder="Data entrata nella famiglia" >
                    </div>
                </div>
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Stato:</label>
                    <div class="col-8">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="stato" id="stato1" value="1" checked>
                      <label class="form-check-label" for="stato1">
                        Includi nel nucleo familiare
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="stato" id="stato2" value="0">
                      <label class="form-check-label" for="stato2">
                        Non includere nel nucleo familiare
                      </label>
                    </div>
                    </div>
                </div>
                <div class="form-group row">
                  <label for="example-text-input" class="col-4 col-form-label">Note:</label>
                    <div class="col-8">
                      <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>
                </div>
              </form>
            </template> 
            <template slot="modal-button">
                  <button class="btn btn-danger" form="formComponenteAggiorna">Salva</button>
            </template>
          </my-modal>  -->
        </div>
      </div>
    </div>
  </div> <!--  end col dati anagrafici -->

  <div class="col-md-4"> <!--  start col dati gruppo -->
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-2" >
          <div class="card-header" id="headingZero">
            <h5 class="mb-0">
              <button class="btn btn-link" data-toggle="collapse" data-target="#collapsezero" aria-expanded="true" aria-controls="collapsezero">
                Gruppo familiare attuale
              </button>
            </h5>
          </div>
          <div id="collapsezero" class="collapse show" aria-labelledby="headingZero" data-parent="#accordion">
            <div class="card-body">
              <ul class="list-group list-group-flush">
                @if($famiglia->gruppoFamiliareAttuale())
                 <li class="list-group-item">
                    <div class="row">
                      <div class="col-sm-4">
                         {{$famiglia->gruppoFamiliareAttuale()->nome}}
                      </div>
                      <div class="col-sm-4">
                        <span> {{$famiglia->gruppoFamiliareAttuale()->pivot->data_inizio}}</span>
                      </div>
                    </div>
                  </li>
                  @else
                  <p class="text-danger">Nessun gruppo familiare associato</p>
                  @endif  
                </ul>
                <!-- <div class="col-md-4"> -->
                    <my-modal modal-title="Sposta in un nuovo gruppo familiare" button-style="btn-primary my-2" button-title="Nuovo gruppo">
                      <template slot="modal-body-slot">
                        <form class="form" method="POST" id="formGruppo" action="{{ route('nomadelfia.famiglie.gruppo.assegna', ['id' =>$famiglia->id]) }}" >      
                          {{ csrf_field() }}
                          <div class="form-group row">
                            <label for="example-text-input" class="col-4 col-form-label">Nuovo gruppo</label>
                              <div class="col-8">
                                <select class="form-control" name="nuovogruppo">
                                <option value="" selected>---scegli gruppo---</option>
                                  @foreach (App\Nomadelfia\Models\GruppoFamiliare::all() as $gruppo)
                                      <option value="{{ $gruppo->id }}">{{ $gruppo->nome }}</option>
                                    @endforeach
                              </select>
                              </div>
                          </div>
                          <div class="form-group row">
                            <label for="example-text-input" class="col-4 col-form-label">Data cambio gruppo:</label>
                              <div class="col-8">
                                <input type="date" class="form-control" name="datacambiogruppo" placeholder="Data cambio gruppo" >
                              </div>
                          </div>
                          <div class="form-group row">
                            <p class="col-4 text-justify"> Le seguenti persone saranno spostate nel gruppo familiare selezionato:</p>
                              <div class="col-8">
                              <ul>
                                @foreach($famiglia->componentiAttuali as $componente)
                                  <li>{{$componente->nominativo}}</li>
                                  @endforeach
                              </ul>
                              </div>
                          </div>
                        </form>
                      </template> 
                      <template slot="modal-button">
                            <button class="btn btn-danger" form="formGruppo">Salva</button>
                      </template>
                    </my-modal>
                 <!-- </div>   end col-md-3 formodal-->
                  
                <!-- </div>  -->
              </div> <!--  end card body -->
            </div>  <!--  end collapse -->
          </div>  <!--  end card -->
        </div> <!--  end colum -->
    </div>  <!--  end first row -->

    
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-2">
          <div class="card-header" id="headingZero">
            <h5 class="mb-0">
              <button class="btn btn-link" data-toggle="collapse" data-target="#collapsezero" aria-expanded="true" aria-controls="collapsezero">
                Gruppo familiare Storico
              </button>
            </h5>
          </div>
          <div id="collapsezero" class="collapse show" aria-labelledby="headingZero" data-parent="#accordion">
            <div class="card-body">
              <ul class="list-group list-group-flush">
                @forelse($famiglia->gruppiFamiliariStorico as $gruppo)
                <li class="list-group-item">
                    <div class="row">
                      <div class="col-sm-8">
                       {{$gruppo->nome}}
                      </div>
                      <div class="col-sm-4">
                        <span> @year($gruppo->pivot->data_inizio) - @year($gruppo->pivot->data_fine)</span>
                      </div>
                    </div>
                  </li>
                  @empty
                  <p class="text-danger">Nessun gruppo familiare storico.</p>
                  @endforelse   
                </ul>
            
            </div> <!--  end card body-->
          </div>  <!--  end collapszero-->
        </div>  <!--  end card -->
      </div> <!--  end 12 colum -->
    </div>  <!--  end second row -->
 
  </div> <!--  end col dati gruppo familiare -->

</div> <!--  end first row-->

@endsection
