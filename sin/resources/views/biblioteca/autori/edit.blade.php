@extends('biblioteca.libri.index')
@section('title', 'Autori')

@section('archivio')

@include('partials.header', ['title' => 'Modifica Autore'])

<div class="row">
  <div class="col-md-6 offset-md-2">
    {{ Form::model($autore, array('route' => array('autori.update', $autore->id), 'method' => 'PUT','class'=>"form-horizontal")) }}
    <div class="form-group">
        {{ Form::label('Autore', 'Autore') }}
        {{ Form::text('Autore', $autore->autore, array('class' => 'form-control','name'=>"autore")) }}
    </div>
    <div class="form-group my-2">
    {{ Form::submit('Salva', array('class' => 'btn btn-primary')) }}
    {{ Form::close() }}
    </div>
  </div>
</div>

@endsection
