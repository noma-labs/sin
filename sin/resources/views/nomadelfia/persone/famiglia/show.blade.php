@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => "Gestione Famiglie di ".$persona->nominativo])

<div class="row justify-content-center">
   <div class="col-md-6">
  
    <div class="card">
      <div class="card-header">
        Famiglia Attuale   
      </div>
      <div class="card-body">
          @if($attuale)
            <div class="row">
              <p class="col-md-3 font-weight-bold"> Nome Famigla </p>
              <p class="col-md-3 font-weight-bold"> Data Entrata</p>
              <p class="col-md-3 font-weight-bold"> Posizione Famiglia</p>
              <p class="col-md-3 font-weight-bold"> Operazioni</p>
            </div>
            <div class="row">
              <p class="col-md-3">  <a href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$attuale->id])}}">{{$attuale->nome_famiglia}}  </a></p>
              <p class="col-md-3">{{$attuale->pivot->data_entrata }} </p>
              <p class="col-md-3">{{$attuale->pivot->posizione_famiglia }} </p>
              <div class="col-md-3"> <a class="btn btn-warning" href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$attuale->id])}}" role="button">Dettaglio</a>
            </div>
          @else
           <p class="text-danger">Nessuna famiglia</p>
          @endif
          
          <my-modal modal-title="Sposta in una nuova Famiglia" button-title="Sposta" button-style="btn-success  my-2">
              <template slot="modal-body-slot">
                <form class="form" method="POST"  id="formFamigliaSposta" action="{{ route('nomadelfia.personae.famiglie.sposta', ['idPersona' =>$persona->id]) }}" >      
                    {{ csrf_field() }}
                      @if($attuale)
                        <h5 class="my-2">Completa dati della famiglia attuale: {{$attuale->nome}}</h5>
                        <div class="form-group row">
                          <label for="inputPassword" class="col-sm-4 col-form-label">Data uscita famiglia</label>
                          <div class="col-sm-8">
                            <date-picker :bootstrap-styling="true" value="" format="yyyy-MM-dd" name="old_data_uscita"></date-picker>
                            <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se concide con la data di entrata nella nuova famiglia .</small>
                          </div>
                        </div>
                        <hr>
                      @endif

                      <div class="form-group row">
                        <label for="staticEmail" class="col-sm-4 col-form-label">Famiglia</label>
                        <div class="col-sm-8">
                          <select name="new_famiglia_id" class="form-control">
                              <option value="" selected>---Seleziona famiglia---</option>
                              @foreach (App\Nomadelfia\Models\Famiglia::ordered() as $famiglia)
                                  <option value="{{$famiglia->id}}">{{$famiglia->nome_famiglia}}</option>
                              @endforeach
                          </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-4 col-form-label">Data entrata famiglia</label>
                        <div class="col-sm-8">
                          <date-picker :bootstrap-styling="true" value="{{ Carbon::now()->toDateString()}}" format="yyyy-MM-dd" name="new_data_entrata"></date-picker>
                        </div>
                      </div>
                      <hr>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-4 col-form-label">Posizione Famiglia</label>
                        <div class="col-sm-8">
                          <select name="new_posizione_famiglia" class="form-control">
                              <option value="" selected>---Seleziona posizione---</option>
                              @foreach (App\Nomadelfia\Models\Famiglia::getEnum('Posizione') as $posizione)
                                <option value="{{ $posizione }}">{{ $posizione }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                </form>
              </template> 
              <template slot="modal-button">
                <button class="btn btn-success" form="formFamigliaSposta">Salva</button>
              </template>
            </my-modal> <!--end modal aggiungi categoria-->
                    
         @if($persona->isMaggiorenne() && !$attuale)
          <my-modal modal-title="Crea Nuova Famiglia" button-title="Crea la sua Famiglia" button-style="btn-success  my-2">
              <template slot="modal-body-slot">
                  <form method="POST" id="formCreaFamiglia"  action="{{route('nomadelfia.personae.famiglie.create',['idPersona'=>$persona->id])}}">   
                    {{ csrf_field() }}
                      <div class="form-group row">
                        <label for="fornome" class="col-md-6 col-form-label">Nome famiglia:</label>
                        <div class="col-md-6">
                          <input class="form-control" id="fornome" name="nome"  value="{{ old('nome') }}" placeholder="Nome famiglia">
                        </div>
                      </div>
                      <div class="form-group row">
                          <label for="fordatainizio" class="col-md-6 col-form-label">Data creazione famiglia:</label>
                          <div class="col-md-6">
                             <date-picker :bootstrap-styling="true"  value="{{ old('data_inizio')? old('data_inizio'): Carbon::now()->toDateString()}}" format="yyyy-MM-dd" name="data_creazione"></date-picker>
                          </div>
                        </div>
                      <div class="form-group row">
                          <label for="staticEmail" class="col-sm-6 col-form-label">Posizione Famiglia</label>
                          <div class="col-sm-6">
                            <select name="posizione_famiglia" class="form-control">
                                <option value="" selected>---Seleziona posizione---</option>
                                @foreach (App\Nomadelfia\Models\Famiglia::getEnum('Posizione') as $posizione)
                                  @if ($posizione == "SINGLE" ||  $posizione == "CAPO FAMIGLIA")
                                    <option value="{{ $posizione }}">{{ $posizione }}</option>
                                  @endif
                              @endforeach
                            </select>
                          </div>
                        </div>
                      <div class="form-group row">
                          <label for="fordatainizio" class="col-md-6 col-form-label">Data entrata famiglia:</label>
                          <div class="col-md-6">
                            <date-picker :bootstrap-styling="true"  value="" format="yyyy-MM-dd" name="data_entrata"></date-picker>
                            <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se la data di entrata nella famigla è uguale alla data di creazione della famiglia </small>
                          </div>
                       </div>
                </form>
              </template> 
              <template slot="modal-button">
                <button class="btn btn-success" form="formCreaFamiglia">Salva</button>
              </template>
            </my-modal> 
            @endif
      </div>  <!--end card body-->
    </div> <!--end card -->
    <div class="card my-3">
      <div class="card-header">
       Storico famiglie
      </div>
      <div class="card-body">
        <div class="row">
          <p class="col-md-3 font-weight-bold"> Nome famiglia</p>
          <p class="col-md-3 font-weight-bold"> Data inizio</p>
          <p class="col-md-3 font-weight-bold"> Data fine </p>
          <p class="col-md-3 font-weight-bold"> Durata  </p>
        </div>

        @forelse($storico as $famigliaStorico)
      
        <div class="row">
          <p class="col-md-3"> {{$famigliaStorico->nome_famiglia}}</p>
          <p class="col-md-3">{{$famigliaStorico->pivot->data_entrata }} </p>
          <p class="col-md-3">{{$famigliaStorico->pivot->data_uscita }} </p>

          <div class="col-md-3">
            <span class="badge badge-info"> 
            {{Carbon::parse($famigliaStorico->pivot->data_uscita)->diffForHumans(Carbon::parse($famigliaStorico->pivot->data_entrata),['short' => true])}}</span>
            </div>
        </div>

        @empty
        <p class="text-danger">Nessuna famiglia nello storico</p>
        @endforelse
      </div>  <!--end card body-->
     </div> <!--end card -->

   </div> <!--end card -->
 </div> <!--end col -md-6 -->
</div> <!--end row justify-->

@endsection
