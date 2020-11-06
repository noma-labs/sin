@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Famiglia'])

<div class="row justify-content-md-center">
    <div class="col-md-12">
    <div class="card">
        <div class="card-body">
          <p class="card-text">Nome famiglia: <span class="font-weight-bold">{{$famiglia->nome_famiglia}}</span></p>
          <p class="card-text">Data creazione:  <span class="font-weight-bold">{{$famiglia->data_creazione}}</span></p>
          @include("nomadelfia.templates.aggiornaFamiglia", ['famiglia' => $famiglia])
        </div>
      </div>
    </div>
</div>

<div class="row my-3">
<div class="col-md-8 mb-2"> <!--  start col dati anagrafici -->
    <div class="card">
      <div class="card-header" id="headingOne">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          Componenti della famiglia
          </button>
        </h5>
      </div>
      <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
        <div class="card-body">
          @include("nomadelfia.templates.famiglia", ['componenti' => $componenti])
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
                          @include('nomadelfia.templates.gruppo', ['id'=>($gruppoAttuale[0])->id, "nome"=>($gruppoAttuale[0])->nome])
                      
                      </div>
                      <div class="col-sm-6">
                        <span> {{$gruppoAttuale[0]->data_entrata_gruppo}}</span>
                        @include('nomadelfia.templates.spostaFamigliaNuovoGruppo', ['famiglia_id'=>$famiglia->id, 'componenti'=>$componenti, "gruppo_id"=>$gruppoAttuale[0]->id])
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
                       @include('nomadelfia.templates.gruppo', ['id'=>$gruppo->id, "nome"=>$gruppo->nome])
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
