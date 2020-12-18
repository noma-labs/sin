@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi Persona'])

<div class="row">
  <div class="col-md-4 offset-md-4">
   <form class="form" method="POST" action="{{ route('nomadelfia.persone.inserimento.entrata.scelta', ['idPersona' =>$persona->id])}}">
     {{ csrf_field() }}
     <persona-entrata 
      api-nomadelfia-famiglie="{{route('api.nomadeflia.famiglie')}}"
      api-nomadelfia-persona="{{route('api.nomadelfia.persona', ['id'=>$persona->id])}}"
      api-nomadelfia-gruppi="{{route('api.nomadeflia.gruppi')}}"
    >
    </persona-entrata>

    <div class="row my-2">
      <div class="col-auto">
        <button type="submit" class="btn btn-block btn-primary">Salva</button> 
      </div>
    </div>
  </form>
</div>  <!-- end col dati anagrafici -->
</div>
@endsection