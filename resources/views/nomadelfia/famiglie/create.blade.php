@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Creazione nuova Famiglia'])

<div class="row">
  <div class="col-md-6 offset-md-3">
  <h4>Dati Famiglia</h4>
  <form method="POST" action="{{route('nomadelfia.famiglie.create.confirm')}}">
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
         <date-picker :bootstrap-styling="true"  value="{{ old('data_inizio') }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
      </div>
    </div>
  <!-- <h4>Componenti della famiglia</h4>
  <div class="form-group row">
      <label for="fornome" class="col-md-4 col-form-label">Persona:</label>
      <div class="col-md-4">
        <autocomplete placeholder="Inserisci nominativo..." name="persona_id" url="{{route('api.nomadeflia.persone.search')}}"></autocomplete>
      </div>
      <div class="col-md-4">
        <select class="form-control" name="posizione">
         <option value="" selected>---scegli posizione---</option>
          @foreach (Domain\Nomadelfia\Famiglia\models\Famiglia::getEnum('Posizione') as $posizione)
              <option value="{{ $posizione }}">{{ $posizione }}</option>
            @endforeach
        </select>
      </div>
    </div> -->

    <div class="row">
      <div class="col-auto">
        <!-- <button class="btn btn-warning" name="_addanother" value="true" type="submit">Salva e aggiungi un'altra </button> -->
        <button class="btn btn-success" name="_addonly" value="true" type="submit">Salva</button> 
      </div>
    </div>
    
  </form>
  </div>
</div>
@endsection
