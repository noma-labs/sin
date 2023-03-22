@extends('biblioteca.libri.index')
@section('title', 'Editori')

@section('archivio')

@include('partials.header', ['title' => 'Modifica Editore'])

<div class="row">
  <div class="col-md-6 offset-md-2">
    {{ Form::model($editore, array('route' => array('editori.update', $editore->id), 'method' => 'PUT','class'=>"form-horizontal")) }}
    <div class="form-group">
        {{ Form::label('Editore', 'Editore') }}
        {{ Form::text('Editore', $editore->editore, array('class' => 'form-control','name'=>"editore")) }}
    </div>
    <div class="form-group">
    {{ Form::submit('Salva', array('class' => 'btn btn-primary')) }}
    {{ Form::close() }}
    </div>
  </div>
</div>

@endsection
