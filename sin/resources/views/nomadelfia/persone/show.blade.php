@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => $persona->nome." ". $persona->cognome])


<div class="row my-3">
 <div class="col-md-3 mb-2"> <!--  start col dati anagrafici -->
    <div class="card">
      <div class="card-header" id="headingOne">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            Dati anagrafici
          </button>
        </h5>
      </div>
      <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
        <div class="card-body">

        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4 font-weight-bold">Nome:</label>
              <div class="col-sm-8">
                <span>{{$persona->nome}} </span>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4 font-weight-bold">Cognome:</label>
              <div class="col-sm-8">
                    <span>{{$persona->cognome}}</span>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4 font-weight-bold">Data Nascita:</label>
              <div class="col-sm-8">
                    <span>{{$persona->data_nascita}}</span>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4 font-weight-bold">Luogo Nascita:</label>
              <div class="col-sm-8">
                  <span>{{$persona->provincia_nascita}}</span>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4 font-weight-bold">Sesso:</label>
              <div class="col-sm-8">
                <span>{{$persona->sesso}}</span>
              </div>
            </div>
          </li>
          @if ($persona->isDeceduta())
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4 font-weight-bold">Data decesso:</label>
              <div class="col-sm-8">
                  <span> {{$persona->getDataDecesso()}} </span>
                </div>
              </div>
            </li>
          @endif 
         
        </ul>
        <a class="btn btn-warning my-2"  href="{{route('nomadelfia.persone.anagrafica.modifica', $persona->id)}}"  role="button">Modifica</a>
        @if (! $persona->isDeceduta())
        <my-modal modal-title="Decesso di persona" button-title="Decesso" button-style="btn-danger my-2">
          <template slot="modal-body-slot">
              <form class="form" method="POST"  id="formDecessoPersona{{$persona->id}}" action="{{ route('nomadelfia.persone.decesso', ['idPersona' =>$persona->id]) }}" >      
                  @csrf
                  <p> Inserire la data di decesso di {{$persona->nominativo}} </p>
                  <date-picker :bootstrap-styling="true" format="yyyy-MM-dd" name="data_decesso"></date-picker>
              </form>
          </template> 
          <template slot="modal-button">
              <button class="btn btn-danger" form="formDecessoPersona{{$persona->id}}">Salva</button>
          </template> 
      </my-modal>
      @endif

                  
       {{-- 
        <my-modal modal-title="Rimuovi persona " button-title="Elimina Persona" button-style="btn-danger my-2">
            <template slot="modal-body-slot">
                <form class="form" method="POST"  id="formEliminaPersona{{$persona->id}}" action="{{ route('nomadelfia.persone.rimuovi', ['idPersona' =>$persona->id]) }}" >      
                    @csrf
                    @method('delete')
                    <body> Vuoi davvero eliminare {{$persona->nominativo}} ? </body>
                </form>
            </template> 
            <template slot="modal-button">
                <button class="btn btn-danger" form="formEliminaPersona{{$persona->id}}" >Elimina</button>
            </template> 
        </my-modal> 
        --}}
        </div>
      </div>
    </div>
  </div> <!--  end col dati anagrafici -->


  <div class="col-md-5"> <!--  start col dati nomadelfia -->
    <div class="card mb-2">
      <div class="card-header" id="headingZero">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapsezero" aria-expanded="true" aria-controls="collapsezero">
            Dati Nomadelfia
          </button>
        </h5>
      </div>
      <div id="collapsezero" class="collapse show" aria-labelledby="headingZero" data-parent="#accordion">
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <div class="row">
                  <label class="col-sm-4 font-weight-bold">Data Entrata:</label>
                  <div class="col-sm-6">
                    <p> {{$persona->getDataEntrataNomadelfia()}}
                      <span class="badge badge-info"> @diffHumans($persona->getDataEntrataNomadelfia()) </span>
                    </p>
                  </div>
                  <div class="col-sm-2">
                    @if($persona->isPersonaInterna())
                      <my-modal modal-title="Uscita dalla comunità" button-title="Uscita" button-style="btn-danger my-2">
                          <template slot="modal-body-slot">
                              <form class="form" method="POST"  id="formUscitaPersona{{$persona->id}}" action="{{ route('nomadelfia.persone.uscita', ['idPersona' =>$persona->id]) }}" >      
                                  @csrf
                                  <p> Inserire la data di uscita di {{$persona->nominativo}} </p>
                                  <date-picker :bootstrap-styling="true" format="yyyy-MM-dd" name="data_uscita"></date-picker>
    
                              </form>
                          </template> 
                          <template slot="modal-button">
                              <button class="btn btn-success" form="formUscitaPersona{{$persona->id}}">Salva</button>
                          </template> 
                      </my-modal> 
                      @endif
                  </div>
                </div>
              </li>
              @if ($persona->getDataUscitaNomadelfia())
              <li class="list-group-item">
                  <div class="row">
                    <label class="col-sm-4 font-weight-bold">Data Uscita:</label>
                    <div class="col-sm-6">
                        <span> {{$persona->getDataUscitaNomadelfia()}} </span>
                    </div>
                    <div class="col-sm-2">
                        <a class="btn btn-warning" href="{{route('nomadelfia.persone.stato', ['idPersona'=>$persona->id])}}">Modifica</a> 
                      </div>
                  </div>
              </li>
              @endif
              
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 font-weight-bold">Nominativo: </label>
                <div class="col-sm-6">
                  {{$persona->nominativo}}
                </div>
                <div class="col-sm-2">
                <a class="btn btn-warning"  href="{{route('nomadelfia.persone.nominativo.modifica', $persona->id)}}"  role="button">Modifica</a>
                </div>
              </div>
            </li>
           
            {{-- <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 font-weight-bold">Categoria: </label>
                <div class="col-sm-6">
                    @if ($categoriaAttuale != null)
                        {{$categoriaAttuale->nome}}
                    @else
                        <span class="text-danger">Nessuna categoria</span>
                    @endif
                </div>
                <div class="col-sm-2">
                  <a class="btn btn-warning my-2"  href="{{route('nomadelfia.persone.categoria', $persona->id)}}"  role="button">Modifica</a>
                </div>
              </div>
            </li> --}}
            
            <li class="list-group-item">
              <div class="row">
                  <label class="col-sm-4 font-weight-bold">Stato familiare:</label>
                  <div class="col-sm-6">
                    @if ($persona->statoAttuale() != null)
                        <span>{{$persona->statoAttuale()->nome}}</span>
                    @else
                        <span class="text-danger">Nessuno stato</span>
                    @endif
                  </div>
                  <div class="col-sm-2">
                    <a class="btn btn-warning" href="{{route('nomadelfia.persone.stato', ['idPersona'=>$persona->id])}}">Modifica</a> 
                  </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 font-weight-bold">Posizione: </label>
                <div class="col-sm-6">
                  @forelse ($persona->posizioneAttuale()->get() as $attuale)
                    @if ($attuale->number == 0)
                     {{$attuale->nome}}
                    @else
                     ,{{$attuale->nome}}
                    @endif
                  @empty
                    <span class="text-danger">Nessuna posizione</span>
                  @endforelse
                </div>
                <div class="col-sm-2">
                  <a class="btn btn-warning" href="{{route('nomadelfia.persone.posizione', ['idPersona'=>$persona->id])}}">Modifica</a> 
                </div>
            </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 font-weight-bold">Gruppo familiare: </label>
                <div class="col-sm-6">
                  @if ($gruppoAttuale)
                      <a href="{{route('nomadelfia.gruppifamiliari.dettaglio', [$gruppoAttuale->id])}}">{{ $gruppoAttuale->nome }} </a> 
                  @else
                    <span class="text-danger">Nessun gruppo</span>
                  @endif
                </div>
                <div class="col-sm-2">
                  <a class="btn btn-warning" href="{{route('nomadelfia.persone.gruppofamiliare', ['idPersona'=>$persona->id])}}">Modifica</a> 
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 font-weight-bold">Azienda/e:</label>
                <div class="col-sm-6">
                  @forelse ($persona->aziendeAttuali()->get() as $azienda)
                      <p> <a href="{{route('nomadelfia.aziende.edit', [$azienda->id])}}">{{ $azienda->nome_azienda }} </a> ({{ $azienda->pivot->mansione }})</p>
                  @empty
                      <span class="text-danger">Nessuna azienda</span>
                  @endforelse
                </div>
                <div class="col-sm-2">
                  <a class="btn btn-warning" href="{{route('nomadelfia.persone.aziende', ['idPersona'=>$persona->id])}}">Modifica</a> 
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div> 
    </div>  <!--  end card -->
  </div> <!--  end col dati nomadelfia -->

 
   <div class="col-md-4"> <!--  start col dati famiglia -->
    <div class="card">
      <div class="card-header" id="headingTwo">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            Dati Famiglia
          </button>
        </h5>
      </div>
      <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
        <div class="card-body">
        
   
        <ul class="list-group list-group-flush">
          @if($famigliaAttuale)
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4 font-weight-bold">Nome Famiglia:</label>
              <div class="col-sm-8">
                 <a  href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$persona->famigliaAttuale()->id])}}"> {{$persona->famigliaAttuale()->nome_famiglia}} </a>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4 font-weight-bold">Posizione:</label>
              <div class="col-sm-8">
                  <span>{{$persona->famigliaAttuale()->pivot->posizione_famiglia}}</span>
              </div>
            </div>
          </li>
        @else
          <li class="list-group-item">
            <div class="row">
              <label class="col-sm-4 font-weight-bold">Nome Famiglia:</label>
              <div class="col-sm-8">
                  <span class="text-danger">Nessuna famiglia</span>
              </div>
            </div>
          </li>
        @endif
        <li class="list-group-item">
            <div class="row">
              <div class="col-sm-8">
                  <a class="btn btn-warning" href="{{route('nomadelfia.persone.famiglie', ['idPersona'=>$persona->id])}}">Modifica</a> 
              </div>
            </div>
          </li>
        </ul>
       
      
        </div>
      </div>
    </div>
  </div> <!--  end col dati famiglia-->
 
</div> <!--  end first row-->

@endsection
