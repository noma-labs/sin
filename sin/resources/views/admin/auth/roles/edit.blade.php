@extends('admin.index')
@section('title', '| Modifica ruolo')

@section('archivio')
@include('partials.header', ['title' => 'Modifica ruolo'])

<form method="POST" action="{{route('roles.update',  $ruolo->id)}}">
    <input type="hidden" name="_method" value="PUT">
    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-6 offset-md-2">
            <h1>PAGINA IN COSTRUZIONE</h1>
            <p class="font-weight-normal">Ruolo:: <b>{{$ruolo->name}}</b></p>
            <!--            <p class="font-weight-normal">Descrizione: <b>{{$ruolo->descrizione}}</b></p>-->
        </div>
    </div>

</form>

@endsection
