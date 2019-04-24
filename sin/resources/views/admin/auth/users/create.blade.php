@extends('admin.index')
@section('title', '| Add User')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi utente'])
<div class='col-lg-4  offset-md-3'>
    {{ Form::open(array('method'=>'POST','route' => 'users.store')) }}
    <div class="form-group">
      {{ Form::label('name', 'Nominativo (Persona anagrafe)(*)') }}
      <!-- <search-persona></search-persona> -->
 <autocomplete placeholder="Inserisci nominativo..." name="persona_id" url={{route('api.nomadeflia.persone.search')}}></autocomplete>
        <!-- {{ Form::text('name', '', array('class' => 'form-control')) }} -->
    </div>

    <div class="form-group">
        {{ Form::label('username', 'Username(*)') }}
        {{ Form::text('username', '', array('class' => 'form-control')) }}
    </div>

    <!-- <div class="form-group">
        {{ Form::label('email', 'Email') }}
        {{ Form::email('email', '', array('class' => 'form-control')) }}
    </div> -->

    <div class='form-group'>
    <h5><b>Assegna i ruoli all'utente</b></h5>
        @foreach ($roles as $role)
            {{ Form::checkbox('roles[]',  $role->id ) }}
            {{ Form::label($role->name, ucfirst($role->nome)) }}<br>

        @endforeach
    </div>

    <div class="form-group">
        {{ Form::label('password', 'Password(*)') }}<br>
        {{ Form::password('password', array('class' => 'form-control')) }}

    </div>

    <div class="form-group">
        {{ Form::label('password', 'Conferma Password(*)') }}<br>
        {{ Form::password('password_confirmation', array('class' => 'form-control')) }}

    </div>
    <p class="text-danger">(*) campi obbligatori</p>
    {{ Form::submit('Aggiungi', array('class' => 'btn btn-primary')) }}

    {{ Form::close() }}

</div>

@endsection
