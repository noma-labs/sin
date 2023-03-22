@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi Persona'])

<div class="row">
  <div class="col-md-4 offset-md-4">
      @include("nomadelfia.templates.entrataPersona", ['persona' => $persona])
  </div>
</div>
@endsection