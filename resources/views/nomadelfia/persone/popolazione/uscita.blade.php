@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => "Uscita persona"])

<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Dettaglio
            </div>
            <div class="card-body">
            <form method="POST"  id="uscitaPersonaForm" action="{{ route('nomadelfia.persone.uscita', ['idPersona' =>$persona->id]) }}">
            @csrf
             <div class="form-group row">
                <label for="persona" class="col-sm-6 col-form-label">Persona</label>
                <div class="col-sm-6">
                       <input type="text" readonly class="form-control-plaintext" id="persona" value="{{$persona->nominativo}}">
                </div>
             </div>
            <div class="form-group row">
                <label for="dataUscita" class="col-sm-6 col-form-label">Data Uscita</label>
                <div class="col-sm-6">
                  <date-picker :bootstrap-styling="true" value="{{ Carbon::now()->toDateString()}}" format="yyyy-MM-dd" name="data_uscita"></date-picker>
                </div>
             </div>
             <div class="form-group row">
                <label for="numeroElenco" class="col-sm-6 col-form-label">Numero di Elenco</label>
                <div class="col-sm-6">
                 <input class="form-control" value="{{$propose}}" name="numero_elenco">
                </div>
              </div>
               <div class="form-group row">
                    <label for="esistenti" class="col-sm-6 col-form-label">Numero elenco precedenti</label>
                     @if(count($assegnati) >0 )
                   <div class="col-sm-6">
                      <select class="col-form-label">
                          @foreach ($assegnati as $p)
                              <option value="{{$p}}"> {{$p->numero_elenco}} {{$p->nome}} {{$p->cognome}}</option>
                          @endforeach
                      </select>
                      @else
                         <p class="text-danger">Non ci sono numero elenco esistenti</p>
                      @endif
                   </div>

                  </div>
            </form>
                <div class="card-footer">
                    <button class="btn btn-primary" form="uscitaPersonaForm">Salva</button>
                </div>
            </div>



            </div>


        </div>
    </div>
</div>
@endsection
