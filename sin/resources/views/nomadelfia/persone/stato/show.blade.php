@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => "Gestione Stato  ".$persona->nominativo])

<div class="row justify-content-center">
  <div class="col-md-6">
    <my-modal modal-title="Aggiungi Stato persona" button-title="Aggiungi Stato" button-style="btn-primary my-2">
      <template slot="modal-body-slot">
      <form class="form" method="POST"  id="formPersonaStato" action="{{ route('nomadelfia.persone.stato.assegna', ['idPersona' =>$persona->id]) }}" >      
          {{ csrf_field() }}

          @if($persona->statoAttuale())
          <h5 class="my-2">Completa dati dello stato attuale: {{$persona->statoAttuale()->nome}</h5>
          <div class="form-group row">
            <label for="dataInizio" class="col-sm-6 col-form-label">Data fine stato</label>
            <div class="col-sm-6">
              <date-picker :bootstrap-styling="true" value="{{ old('data_fine') }}" format="yyyy-MM-dd" name="data_fine"></date-picker>
              <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se concide con la data di inizio del nuovo stato.</small>
            </div>
          </div>
          <hr>
          @endif
          <h5 class="my-2">Inserimento nuovo stato</h5>
          <div class="form-group row">
            <label for="staticEmail" class="col-sm-4 col-form-label">Stato</label>
            <div class="col-sm-8">
              <select name="stato_id" class="form-control">
                  <option selecte>---seleziona posizione---</option>
                  @foreach (App\Nomadelfia\Models\Stato::all() as $stato)
                    <option value="{{$stato->id}}">{{$stato->nome}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="inputPassword" class="col-sm-4 col-form-label">Data inizio</label>
            <div class="col-sm-8">
              <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
            </div>
          </div>
        </form>
      </template> 
      <template slot="modal-button">
        <button class="btn btn-success" form="formPersonaStato">Salva</button>
      </template>
    </my-modal> <!--end modal-->

    <div class="card">
      <div class="card-header">
        Stato attuale
      </div>
      <div class="card-body">
          @if($persona->statoAttuale())
            <div class="row">
              <p class="col-md-4 font-weight-bold"> Stato</p>
              <p class="col-md-6 font-weight-bold"> Data Inizio</p>
              <p class="col-md-2 font-weight-bold"> Giorni </p>
            </div>
            <div class="row">
              <p class="col-md-4"> {{$persona->statoAttuale()->nome}}</p>
              <p class="col-md-6">{{$persona->statoAttuale()->pivot->data_inizio }} </p>
              <div class="col-md-2">
                <span class="badge badge-info"> @diffdays($persona->statoAttuale()->pivot->data_inizio)</span>
               </div>
            </div>
          @else
           <p class="text-danger">Nessuno stato</p>
          @endif
      </div>  <!--end card body-->
    </div> <!--end card -->
    <div class="card my-3">
      <div class="card-header">
       Storico delle Stato 
      </div>
      <div class="card-body">
        <div class="row">
          <p class="col-md-3 font-weight-bold"> Stato</p>
          <p class="col-md-3 font-weight-bold"> Data inizio</p>
          <p class="col-md-3 font-weight-bold"> Data fine </p>
          <p class="col-md-3 font-weight-bold"> Durata  </p>
        </div>

        @forelse($persona->statiStorico as $statostorico)
      
        <div class="row">
          <p class="col-md-3"> {{$statostorico->nome}}</p>
          <p class="col-md-3">{{$statostorico->pivot->data_inizio }} </p>
          <p class="col-md-3">{{$statostorico->pivot->data_fine }} </p>

          <div class="col-md-3">
            <span class="badge badge-info"> 
            {{Carbon::parse($statostorico->pivot->data_fine)->diffForHumans(Carbon::parse($statostorico->pivot->data_inizio),['short' => true,'parts'=>5])}}</span>
            </div>
        </div>

        @empty
        <p class="text-danger">Nessuno stato nello storico</p>
        @endforelse
      </div>  <!--end card body-->
     </div> <!--end card -->

   </div> <!--end card -->
 </div> <!--end col -md-6 -->
</div> <!--end row justify-->

@endsection
