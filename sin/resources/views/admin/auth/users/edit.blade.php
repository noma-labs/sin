@extends('admin.index')

@section('title', '| Edit User')

@section('archivio')
@include('partials.header', ['title' => 'Modifica utente'])

{{ Form::model($user, array('route' => array('users.update', $user->id), 'method' => 'PUT')) }}
<div class="row">
    <div class='col-md-3 offset-md-1'>
        <div class="form-group">
            {{ Form::label('name', 'Nominativo(*)') }}
            <autocomplete placeholder="Inserisci nominativo..." 
                :selected="{{$user->persona()->pluck('nominativo', 'id')}}"
                name="persona_id" 
                url={{route('api.biblioteca.clienti')}}>
        </autocomplete>
            <!-- <search-persona-selected personaid={{ $user->persona->id}} nominativo={{$user->persona->nominativo}}> </search-persona-selected> -->
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
                <!-- <input type='hidden' name="roles[{{$role->id}}]" value='0'/> -->
                <input type="checkbox" name="roles[]" value={{$role->id}} {{ $user->hasRole($role) ? 'checked' : '' }}> 
                {{$role->nome}} <br>
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
