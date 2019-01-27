@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => "Gestione Gruppo Familiare  ".$persona->nominativo])

<div class="row justify-content-center">
   <div class="col-md-6">
    <my-modal modal-title="Aggiungi Gruppo Familiare" button-title="Nuovo Gruppo" button-style="btn-success my-2">
      <template slot="modal-body-slot">
      <form class="form" method="POST"  id="formPersonaGruppo" action="{{ route('nomadelfia.persone.gruppo.assegna', ['idPersona' =>$persona->id]) }}" >      
          {{ csrf_field() }}

          @if($persona->gruppofamiliareAttuale())
          <h5 class="my-2">Completa dati del gruppo attuale:  {{$persona->gruppofamiliareAttuale()->nome}}</h5>
          <div class="form-group row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Data uscita gruppo familiare</label>
            <div class="col-sm-6">
              <date-picker :bootstrap-styling="true" value="{{ old('data_uscita') }}" format="yyyy-MM-dd"name="data_uscita"></date-picker>
              <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se concide con la data di entrata nel nuovo gruppo familiare.</small>
            </div>
          </div>
          <hr>
          @endif
          <h5 class="my-2">Nuovo gruppo familiare</h5>
          <div class="form-group row">
            <label for="staticEmail" class="col-sm-6 col-form-label">Gruppo familiare</label>
            <div class="col-sm-6">
              <select name="gruppo_id" class="form-control">
                  <option selected>---seleziona gruppo ---</option>
                  @foreach (App\Nomadelfia\Models\GruppoFamiliare::all() as $gruppofam)
                  @if(!$gruppofam->is($persona->gruppofamiliareAttuale()))
                     <option value="{{$gruppofam->id}}" {{ old('gruppo_id') == $gruppofam->id ? 'selected' : '' }}>{{$gruppofam->nome}}</option>
                    @endif
                  @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="inputPassword" class="col-sm-6 col-form-label">Data entrata gruppo familiare</label>
            <div class="col-sm-6">
              <date-picker :bootstrap-styling="true" value="{{ old('data_entrata') }}" format="yyyy-MM-dd"name="data_entrata"></date-picker>
            </div>
          </div>
        </form>
      </template> 
      <template slot="modal-button">
        <button class="btn btn-success" form="formPersonaGruppo">Salva</button>
      </template>
    </my-modal> <!--end modal-->

    <div class="card">
      <div class="card-header">
        Gruppo Familiare attuale
      </div>
      <div class="card-body">
          @if($persona->gruppofamiliareAttuale())
            <div class="row">
              <p class="col-md-4 font-weight-bold"> Gruppo Familiare</p>
              <p class="col-md-6 font-weight-bold"> Data entrata</p>
              <p class="col-md-2 font-weight-bold"> Giorni </p>
            </div>
            <div class="row">
              <p class="col-md-4"> {{$persona->gruppofamiliareAttuale()->nome}}</p>
              <p class="col-md-6">{{$persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo }} </p>
              <div class="col-md-2">
                <span class="badge badge-info"> @diffdays($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)</span>
               </div>
            </div>
          @else
           <p class="text-danger">Nessun gruppo familiare</p>
          @endif
      </div>  <!--end card body-->
    </div> <!--end card -->
    <div class="card my-3">
      <div class="card-header">
       Storico dei gruppi familiari
      </div>
      <div class="card-body">
        <div class="row">
          <p class="col-md-3 font-weight-bold"> Gruppo</p>
          <p class="col-md-3 font-weight-bold"> Data inizio</p>
          <p class="col-md-3 font-weight-bold"> Data fine </p>
          <p class="col-md-3 font-weight-bold"> Tempo trascorso</p>
        </div>

        @forelse($persona->gruppofamiliariStorico as $gruppostorico)
      
        <div class="row">
          <p class="col-md-3"> {{$gruppostorico->nome}}</p>
          <p class="col-md-3">{{$gruppostorico->pivot->data_entrata_gruppo }} </p>
          <p class="col-md-3">{{$gruppostorico->pivot->data_uscita_gruppo }} </p>

          <div class="col-md-3">
            <span class="badge badge-info"> 
            {{Carbon::parse($gruppostorico->pivot->data_uscita_gruppo)->diffForHumans(Carbon::parse($gruppostorico->pivot->data_entrata_gruppo),['short' => true])}}</span>
            </div>
        </div>

        @empty
        <p class="text-danger">Nessuna storico</p>
        @endforelse
      </div>  <!--end card body-->
     </div> <!--end card -->

   </div> <!--end card -->
 </div> <!--end col -md-6 -->
</div> <!--end row justify-->

@endsection
