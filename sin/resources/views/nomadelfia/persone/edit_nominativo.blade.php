@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Modifica Dati anagrafici'])

<div class="row">
    <div class="col-md-8">
    <form class="form" method="POST" action="{{ route('nomadelfia.persone.nominativo.modifica', ['idPersona' =>$persona->id]) }}" >      
        <div class="form-group row">
          <label for="inputPassword" class="col-sm-4 col-form-label">Nominativo Attuale:</label>
          <div class="col-sm-4">
          <input type="text" class="form-control" name="nominativo" value="{{old('nominativo') ? old('nominativo'): $persona->nominativo}}">
          </div>
        </div>
        <div class="form-group row">
          <label for="inputPassword" class="col-sm-4 col-form-label">Nuovo Nominativo:</label>
          <div class="col-sm-4">
          <input type="text" class="form-control" name="nuovonominativo" value="{{old('nuovonominativo')}}">
          </div>
        </div>
        <!-- <div class="form-group row">
          <label for="inputPassword" class="col-sm-4 col-form-label">Data inserimento nuovo nominativo:</label>
          <div class="col-sm-4">
          <date-picker name="data_inserimento" 
									:bootstrap-styling="true" 
									:language="language" 
									:format="customFormatter"
									:disabled="disabledAll">
						</date-picker>
          </div> -->
        <!-- </div> -->
        <button type="submit" class="btn btn-primary">Salva</button>
      </form>
    </div>
    
    <div class="col-md-4">
    <div class="card">
          <h5 class="card-header">Storico nominativi</h5>
        <div class="card-body">
          <p>Data inserimento, Nominativo</p>

          @forelse  ($persona->nominativiStorici()->get() as $nominativo)
             <li>{{$nominativo->data_inserimento}}, {{$nominativo->nominativo}}</li>
          @empty
          </ol>       
          <p class="text-danger">non ci sono nominativi storici</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>


@endsection
