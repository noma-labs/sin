@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi Persona'])

<form class="form" method="POST" action="{{ route('nomadelfia.persone.inserimento.entrata.scelta', ['idPersona' =>$persona->id])}}">
  {{ csrf_field() }}

  <persona-entrata 
    api-nomadelfia-famiglie="{{route('api.nomadeflia.famiglie')}}"
    api-nomadelfia-persona="{{route('api.nomadelfia.persona', ['id'=>$persona->id])}}"
    api-nomadelfia-gruppi="{{route('api.nomadeflia.gruppi')}}"
  >
  </persona-entrata>

  <div class="form-row">
    <div class="col-md-2 offset-md-6">
      <button type="submit" class="btn btn-block btn-primary">Salva</button>
    </div>
  </div>
</form>
@endsection