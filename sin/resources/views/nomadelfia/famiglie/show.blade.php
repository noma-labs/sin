@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Famiglia'])

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
         
          @if($famiglia->single())
                @include("nomadelfia.templates.famigliaSingle", ['single' => $famiglia])
          @elseif ($famiglia->capofamiglia())
                @include("nomadelfia.templates.famigliaCapofamiglia", ['famiglia' => $famiglia])
          @else
              <p class="text-danger">ATTENZIONE: La famiglia non ha un CAPO FAMIGLIA o un SINGLE.</p>
              @include("nomadelfia.templates.famigliaOther", ['famiglia' => $famiglia])
          @endcan
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
                @if(count($gruppoAttuale) === 1)
                <div class="row">
                    <div class="col-sm-6 font-weight-bold">Gruppo familiare </div>
                    <div class="col-sm-6 font-weight-bold">Data entrata   </div>
                  </div>
                
                  <div class="row">
                    <div class="col-sm-6">
                       {{$gruppoAttuale[0]->nome}}
                    </div>
                    <div class="col-sm-6">
                      <span> {{$gruppoAttuale[0]->data_entrata_gruppo}}</span>
                    </div>
                  </div>
              @elseif (count($gruppoAttuale) > 1)
               <p class="text-danger">La famiglia ha multipli gruppi attivi: </p>
               <p>
                  @foreach  ($gruppoAttuale as $gruppo)
                  {{$gruppo->nome}},
                  @endforeach
               </p>
      
              @else
               <p class="text-danger">Nessun gruppo familiare associato</p>
              @endif
               
                <!-- <div class="col-md-4"> -->
                    <my-modal modal-title="Sposta Famiglia in un nuovo gruppo familiare" button-style="btn-success my-2" button-title="Assegna nuovo gruppo">
                      <template slot="modal-body-slot">
                        <form class="form" method="POST" id="formGruppo" action="{{ route('nomadelfia.famiglie.gruppo.assegna', ['id' =>$famiglia->id]) }}" >      
                          {{ csrf_field() }}
                          <div class="form-group row">
                            <label for="example-text-input" class="col-4 col-form-label">Nuovo gruppo</label>
                              <div class="col-8">
                                <select class="form-control" name="nuovo_gruppo_id">
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
                                <date-picker :bootstrap-styling="true" value="{{ old('datacambiogruppo') }}" format="yyyy-MM-dd" name="data_cambiogruppo"></date-picker>
                              </div>
                          </div>
                          <div class="form-group row">
                            <div class="col">
                            <div class="text-justify"> Le seguenti persone saranno spostate nel gruppo familiare selezionato:</p>
                                <ul >
                                  @foreach($famiglia->componentiAttuali as $componente)
                                    <li>{{$componente->nominativo}}</li>
                                    @endforeach
                                </ul>
                            </div>
                          </div>
                          </div>
                        </form>
                      </template> 
                      <template slot="modal-button">
                            <button class="btn btn-success" form="formGruppo">Salva</button>
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
              <div class="row">
                   <div class="col-md-6 font-weight-bold">Gruppo familiare </div>
                  <div class="col-md-6 font-weight-bold"> Data entrata - data uscita   </div>
              </div>
              <ul class="list-group list-group-flush">
                @forelse($gruppiStorici as $gruppo)
                <li class="list-group-item">
                    <div class="row">
                      <div class="col-md-6">
                       {{$gruppo->nome}}
                      </div>
                      <div class="col-md-6">
                        <span> {{$gruppo->data_entrata_gruppo}} - {{$gruppo->data_uscita_gruppo}}</span>
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
