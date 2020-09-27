@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Assegnazione famiglia'])
<form method="POST" action="{{route('nomadelfia.persone.inserimento.famiglia.confirm',['idPersona'=>$persona->id])}}">
<div class="row">
  <div class="col-md-4 offset-md-4">
    <div class="form-group row">
        <label for="staticEmail" class="col-sm-4 col-form-label">Famiglia</label>
        <div class="col-sm-8">
          <select name="famiglia_id" class="form-control">
              <option value="" selected>---Seleziona famiglia---</option>
              @foreach (App\Nomadelfia\Models\Famiglia::all() as $famiglia)
                <option value="{{$famiglia->id}}">{{$famiglia->nome_famiglia}}</option>
              @endforeach
          </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="staticEmail" class="col-sm-4 col-form-label">Posizione Famiglia</label>
        <div class="col-sm-8">
          <select name="posizione_famiglia" class="form-control">
              <option value="" selected>---Seleziona posizione---</option>
              @foreach (App\Nomadelfia\Models\Famiglia::getEnum('Posizione') as $posizione)
                <option value="{{ $posizione }}">{{ $posizione }}</option>
            @endforeach
          </select>
        </div>
      </div>
  </div>
</div>

{{ csrf_field() }}  
  <div class="row offset-md-4">
    <button class="btn btn-success" type="submit">Salva e visualizza</button> 
  </div>
</form>


@endsection