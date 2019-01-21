@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => "Gestione Posizione  ".$persona->nominativo])

<div class="row justify-content-center">
   <div class="col-md-6">
    <my-modal modal-title="Aggiungi Stato persona" button-title="Aggiungi Posizione" button-style="btn-primary my-2">
      <template slot="modal-body-slot">
      <form class="form" method="POST"  id="formPersonaPosizione" action="{{ route('nomadelfia.persone.posizione.assegna', ['idPersona' =>$persona->id]) }}" >      
          {{ csrf_field() }}

          @if($persona->posizioneAttuale())
          <h5 class="my-2">Completa dati della posizione attuale: {{$persona->posizioneAttuale()->nome}}</h5>
          <div class="form-group row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Data fine posizione</label>
            <div class="col-sm-6">
              <date-picker :bootstrap-styling="true" value="{{ old('data_fine') }}" format="yyyy-MM-dd" name="data_fine"></date-picker>
              <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se concide con la data di inizio della nuova posizione .</small>
            </div>
          </div>
          <hr>
          @endif
          <h5 class="my-2">Inserimento nuova posizione</h5>
          <div class="form-group row">
            <label for="staticEmail" class="col-sm-4 col-form-label">Posizione</label>
            <div class="col-sm-8">
              <select name="posizione_id" class="form-control">
                  <option selecte>---seleziona posizione---</option>
                  @foreach (App\Nomadelfia\Models\Posizione::all() as $posizione)
                    <option value="{{$posizione->id}}">{{$posizione->nome}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Data inizio</label>
            <div class="col-sm-8">
              <!-- <input type="date" name="data_inizio" class="form-control" id="inputPassword" placeholder="Password"> -->
              <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
            </div>
          </div>
        </form>
      </template> 
      <template slot="modal-button">
        <button class="btn btn-success" form="formPersonaPosizione">Salva</button>
      </template>
    </my-modal> <!--end modal-->

    <div class="card">
      <div class="card-header">
        Posizione attuale
      </div>
      <div class="card-body">
          @if($persona->posizioneAttuale())
            <div class="row">
              <p class="col-md-4 font-weight-bold"> Posizione</p>
              <p class="col-md-6 font-weight-bold"> Data Inizio</p>
              <p class="col-md-2 font-weight-bold"> Giorni </p>
            </div>
            <div class="row">
              <p class="col-md-4"> {{$persona->posizioneAttuale()->nome}}</p>
              <p class="col-md-6">{{$persona->posizioneAttuale()->pivot->data_inizio }} </p>
              <div class="col-md-2">
                <span class="badge badge-info"> @diffdays($persona->posizioneAttuale()->pivot->data_inizio)</span>
               </div>
            </div>
          @else
           <p class="text-danger">Nessuna posizione</p>
          @endif
      </div>  <!--end card body-->
    </div> <!--end card -->
    <div class="card my-3">
      <div class="card-header">
       Storico delle Posizione 
      </div>
      <div class="card-body">
        <div class="row">
          <p class="col-md-3 font-weight-bold"> Posizione</p>
          <p class="col-md-3 font-weight-bold"> Data inizio</p>
          <p class="col-md-3 font-weight-bold"> Data fine </p>
          <p class="col-md-3 font-weight-bold"> Durata  </p>
        </div>

        @forelse($persona->posizioniStorico as $posizionestor)
      
        <div class="row">
          <p class="col-md-3"> {{$posizionestor->nome}}</p>
          <p class="col-md-3">{{$posizionestor->pivot->data_inizio }} </p>
          <p class="col-md-3">{{$posizionestor->pivot->data_fine }} </p>

          <div class="col-md-3">
            <span class="badge badge-info"> 
            {{Carbon::parse($posizionestor->pivot->data_fine)->diffForHumans(Carbon::parse($posizionestor->pivot->data_inizio),['short' => true])}}</span>
            </div>
        </div>

        @empty
        <p class="text-danger">Nessuna posizione nello storico</p>
        @endforelse
      </div>  <!--end card body-->
     </div> <!--end card -->

   </div> <!--end card -->
 </div> <!--end col -md-6 -->
</div> <!--end row justify-->

@endsection
