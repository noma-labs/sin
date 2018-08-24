@extends('admin.index')
@section('title', '| Aggiungi Ruolo')

@section('archivio')
@include('partials.header', ['title' => 'Aggiugni ruolo'])

 
<form method="post" action="{{ route('roles.store') }}">
    {{ csrf_field() }}
    <div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="form-group">
            {{ Form::label('nome', 'Nome del ruolo') }}
            {{ Form::text('nome', null, array('class' => 'form-control')) }}
        </div>
        <div class="form-group">
            {{ Form::label('descrizione', 'Descrizione ruolo') }}
            {{ Form::text('descrizione', null, array('class' => 'form-control')) }}
        </div>
    </div>
    </div>

<div class="row">
  <div class="col-md-8 offset-md-2">
  <h4>Assegna le risorsa al nuovo ruolo specificando i permessi</strong></h4>
@foreach ($risorse_per_sistema as $sistema)
<p class="text-secondary">Risorse associate al sistema: <b>{{$sistema->nome}}</b></p>
<table class='table table-bordered table-hover table-sm'>
      <thead class='thead-inverse'>
        <tr>
          <th>Risorsa</th>
          <th>Visualizza</th>
          <th>Inserisci</th>
          <th>Elimina</th>
          <th>Modifica</th>
          <th>Prenota</th>
          <th>Esporta</th>
          <th>Svuota</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($sistema->risorse as $risorsa)
            <tr> 
            <td>{{$risorsa->nome}}</td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[visualizza]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[visualizza]" value='1' />
            </td>       
            <td>
              <input type='hidden' name="{{$risorsa->id}}[inserisci]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[inserisci]" value='1' />
            </td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[elimina]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[elimina]" value='1'/></td>
            </td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[modifica]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[modifica]" value='1'/>
            </td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[prenota]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[prenota]" value='1'/>
            </td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[esporta]" value='0' />
              <input type='checkbox' name="{{$risorsa->id}}[esporta]" value='1'/>
            </td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[svuota]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[svuota]" value='1'/>            
            </td> 
          </tr>
        @endforeach
      </tbody>
    </table>
  @endforeach
 {{ Form::submit('Aggiungi', array('class' => 'btn btn-primary')) }}
 </div>
</div>
{{ Form::close() }}
@endsection
