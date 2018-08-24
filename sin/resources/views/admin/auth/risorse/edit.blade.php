@extends('admin.index')
@section('title', '| Edit Risorse')

@section('archivio')

@include('partials.header', ['title' => 'Modifica permessi'])
<div class='col-md-6 offset-md-4'>
  
    {{ Form::model($risorsa, array('route' => array('risorse.update', $risorsa->id), 'method' => 'PUT')) }}{{-- Form model binding to automatically populate our fields with risorsa data --}}

    <div class="form-group">
        {{ Form::label('name', 'Nome permesso') }}
        {{ Form::text('name', $risorsa->nome, array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
      {{ Form::label('sistema_id', 'Archivio') }}
      {{ Form::select('sistema_id', $sistemi, $risorsa->sistema->id, array('class'=>'form-control'))}}
    </div>
    <br>
    {{ Form::submit('Modifica', array('class' => 'btn btn-primary')) }}
    {{ Form::close() }}
</div>
@endsection
