@extends('admin.index')

@section('title', '| Edit User')

@section('archivio')
@include('partials.header', ['title' => 'Modifica utente'])

{{ Form::model($user, array('route' => array('users.update', $user->id), 'method' => 'PUT')) }}
<div class="row">
    <div class='col-md-3 offset-md-1'>
        <div class="form-group">
            {{ Form::label('name', 'Nominativo(*)') }}
            <autocomplete placeholder="Inserisci nominativo..." name="persona_id"
                          url={{route('api.nomadeflia.popolazione.search')}}></autocomplete>

        </div>
    </div>
    <div class='col-md-3'>
        <div class="form-group">
            {{ Form::label('username', 'Username(*)') }}
            {{ Form::text('username', null, array('class' => 'form-control')) }}
        </div>
    </div>
    <div class='col-md-3'>
        <div class="form-group">
            {{ Form::label('email', 'Email') }}
            {{ Form::email('email', null, array('class' => 'form-control')) }}
        </div>
    </div>
</div>
<div class="row">
    <div class='col-md-3 offset-md-1'>
        <h5><b>Assegna i ruoli all'utente</b></h5>
        <div class='form-group'>
            @foreach ($roles as $role)
            <input type="checkbox" name="roles[]" value={{$role->id}} {{ $user->hasRole($role) ? 'checked' : '' }}>
            {{$role->name}} <br>
            @endforeach
        </div>
    </div>
    <div class='col-md-3'>
        <div class="form-group">
            {{ Form::label('password', 'Password') }}<br>
            {{ Form::password('password', array('class' => 'form-control')) }}
        </div>
    </div>

    <div class='col-md-3'>
        <div class="form-group">
            {{ Form::label('password', 'Conferma Password') }}<br>
            {{ Form::password('password_confirmation', array('class' => 'form-control')) }}
        </div>
    </div>

</div>
<div class="row">
    <div class='col-md-3 offset-md-1'>
        {{ Form::submit('Salva', array('class' => 'btn btn-primary')) }}
        {{ Form::close() }}
    </div>
</div>

@endsection
