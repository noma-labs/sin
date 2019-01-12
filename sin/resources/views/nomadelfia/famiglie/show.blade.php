@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Famiglia'])

<div class="container">
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <label class="col-md-4">Stato attuale:</label> 
          <div class="col-md-4">
              @if($famiglia->stato = '1')
                <div class="p-1 bg-success text-white">Attivo</div>
              @else
                <div class="p-1 bg-danger text-white">Disattivo</div>
              @endif
          </div>
          <div class="col-md-2">
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
</div>

<div class="row my-3">
<div class="col-md-4 mb-2"> <!--  start col dati anagrafici -->
    <div class="card">
      <div class="card-header" id="headingOne">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            Componenti
          </button>
        </h5>
      </div>
      <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
        <div class="card-body">
            @if($famiglia->capofamiglia())
            <div>
              <label for="">Capo Famiglia:</label>
              {{$famiglia->capofamiglia()->nominativo}}
            </div>
            @endif
            @if($famiglia->moglie())
            <div>
              <label for="">Moglie:</label>
              {{$famiglia->moglie()->nominativo}}
            </div>
            @endif
            <ul class="list-group list-group-flush">
            @foreach($famiglia->figli as $figlio)
              <li class="list-group-item">
                <div class="row">
                  <div class="col-sm-8">
                    <span> {{$figlio->data_nascita}} {{$figlio->nome}} {{$figlio->cognome}}  </span>
                  </div>
                  <div class="col-sm-4">
                  @if($figlio->pivot->stato == '1')
                      <div class="p-1 bg-success text-white">Attivo</div>
                    @else
                      <div class="p-1 bg-danger text-white">Disattivo</div>
                    @endif
                  </div>
                </div>
              </li>
              @endforeach    
            </ul>
          <!-- <div class="row"> -->
            <a class="btn btn-primary my-2"  role="button">Modifica</a>
          <!-- </div> -->
        </div>
      </div>
    </div>
  </div> <!--  end col dati anagrafici -->

  <div class="col-md-5"> <!--  start col dati nomadelfia -->
    <div class="card mb-2">
      <div class="card-header" id="headingZero">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapsezero" aria-expanded="true" aria-controls="collapsezero">
            Altri dati
          </button>
        </h5>
      </div>
      <div id="collapsezero" class="collapse show" aria-labelledby="headingZero" data-parent="#accordion">
        <div class="card-body">
        <label for="">  Gruppo Attuale: {{$famiglia->gruppoFamiliareAttuale()->nome}}</label>
        <label for="">  Gruppo Storici</label>
        <ul class="list-group list-group-flush">
            @foreach($famiglia->gruppiFamiliariStorico as $gruppo)
              <li class="list-group-item">
                <div class="row">
                  <div class="col-sm-8">
                    <span>{{ $gruppo->nome}}</span>
                  </div>
                  <div class="col-sm-4">
                  
                  </div>
                </div>
              </li>
              @endforeach    
            </ul>
         
        </div>
      </div> 
    </div>  <!--  end card -->
  </div> <!--  end col dati nomadelfia -->

</div> <!--  end first row-->

@endsection
