{{-- \resources\views\risorse\create.blade.php --}}
@extends('admin.index')

@section('title', '| Aggiungi Permisso')

@section('archivio')

<div class='col-lg-4 col-lg-offset-4'>

    <h1><i class='fa fa-key'></i> Aggiungi Permesso</h1>
    <br>

    {{ Form::open(array('url' => 'risorse')) }}

    <div class="form-group">
        {{ Form::label('name', 'Nome') }}
        {{ Form::text('name', '', array('class' => 'form-control')) }}
    </div><br>
    <div class="form-group">
        {{ Form::label('_belong_to_archivio', "Permesso dell'Archivio") }}
        {{ Form::select('_belong_to_archivio', array('biblioteca' => 'biblioteca', 'rtn' => 'rtn'), null, array('class'=>'form-control'))}}
        <!-- {{ Form::text('archivio', '', array('class' => 'form-control')) }} -->
    </div><br>
    @if(!$roles->isEmpty())
        <h4>Assegna il permesso ai ruoli</h4>

        @foreach ($roles as $role)
            {{ Form::checkbox('roles[]',  $role->id ) }}
            {{ Form::label($role->name, ucfirst($role->name)) }}<br>

        @endforeach
    @endif
    <br>
    {{ Form::submit('Aggiungi', array('class' => 'btn btn-primary')) }}

    {{ Form::close() }}

</div>

@endsection
