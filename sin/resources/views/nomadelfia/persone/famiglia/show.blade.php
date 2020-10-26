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
              <p class="col-md-3 font-weight-bold"> Nome Famigla</p>
              <p class="col-md-3 font-weight-bold"> Data Entrata</p>
              <p class="col-md-3 font-weight-bold"> Posizione Famiglia</p>
              <p class="col-md-3 font-weight-bold"> Operazioni</p>
            </div>
            <div class="row">
              <p class="col-md-3">  <a href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$attuale->id])}}">{{$attuale->nome_famiglia}}  </a></p>
              <p class="col-md-3">{{$attuale->pivot->data_entrata }} </p>
              <p class="col-md-3">{{$attuale->pivot->posizione_famiglia }} </p>
              <div class="col-md-3">     <a class="btn btn-warning" href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$attuale->id])}}" role="button">Dettaglio</a>
            </div>
          @else
           <p class="text-danger">Nessuna famiglia</p>
          @endif

          @if($attuale)
          <my-modal modal-title="Sposta in una Famiglia" button-title="Sposta in Altra Famiglia" button-style="btn-success  my-2">
              <template slot="modal-body-slot">
                <form class="form" method="POST"  id="formPersonaCategoria" action="{{ route('nomadelfia.persone.categoria.assegna', ['idPersona' =>$persona->id]) }}" >      
                    {{ csrf_field() }}
                </form>
              </template> 
              <template slot="modal-button">
                <button class="btn btn-success" form="formPersonaCategoria">Salva</button>
              </template>
            </my-modal> <!--end modal aggiungi categoria-->

          @else
          <my-modal modal-title="Crea Nuova Famiglia" button-title="Crea Famiglia" button-style="btn-success  my-2">
              <template slot="modal-body-slot">
                <form class="form" method="POST"  id="formPersonaCategoria" action="{{ route('nomadelfia.persone.categoria.assegna', ['idPersona' =>$persona->id]) }}" >      
                    {{ csrf_field() }}
                    @if($persona->categoriaAttuale())
                    <h5 class="my-2">Completa dati della categoria attuale: {{$persona->categoriaAttuale()->nome}}</h5>
                    <div class="form-group row">
                      <label for="inputPassword" class="col-sm-6 col-form-label">Data fine categoria</label>
                      <div class="col-sm-6">
                        <date-picker :bootstrap-styling="true" value="{{ Carbon::now()->toDateString()}}" format="yyyy-MM-dd" name="data_fine"></date-picker>
                        <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se concide con la data di inizio della nuova categoria .</small>
                      </div>
                    </div>
                    <hr>
                    @endif
                    <h5 class="my-2">Inserimento nuova categoria</h5>
                    <div class="form-group row">
                      <label for="staticEmail" class="col-sm-6 col-form-label">Categoria</label>
                      <div class="col-sm-6">
                        <select name="categoria_id" class="form-control">
                            <option value="" selected>---seleziona categoria---</option>
                            @foreach ($persona->categoriePossibili() as $categoria)
                              <option value="{{$categoria->id}}">{{$categoria->nome}}</option>
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
                <button class="btn btn-success" form="formPersonaCategoria">Salva</button>
              </template>
            </my-modal> <!--end modal aggiungi categoria-->
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
