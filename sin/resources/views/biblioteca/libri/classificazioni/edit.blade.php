@extends('biblioteca.libri.index')
@section('title', 'Classificazione')

@section('archivio')

@include('partials.header', ['title' => 'Modifica Classificazione'])

<div class="row">
  <div class="col-md-6 offset-md-4">
    {{ Form::model($classificazione, array('route' => array('classificazioni.update', $classificazione->id), 'method' => 'PUT','class'=>"form-horizontal")) }}
    <div class="form-group">
        {{ Form::label('descrizione', 'Classificazione') }}
        {{ Form::text('descrizione', $classificazione->descrizione, array('class' => 'form-control','name'=>"descrizione")) }}
    </div>
    <div class="form-group">
    {{ Form::submit('Salva', array('class' => 'btn btn-primary')) }}
    {{ Form::close() }}
    </div>
  </div>
</div>

@endsection
