@extends('layouts.app')

@section('title', 'rtn')

@section("archivioname","Inserimento film")

@section('archivio')
  <div class="list-group">
   <form method="POST" class="form-inline" action="/rtn/archprof">
       {{ csrf_field() }}
       <div class="form-group">
         <label for="xCategoria" class="control-label">ID </label>
          <select class="form-control"   name="xCategoria" type="text" id="xCategoria">
           @foreach ($categorie as $categoria)
              <option> {{ $categoria->Sotto_categoria}}-{{ $categoria->Descrizione}}</option>
            @endforeach
           </select>
          <!-- @if ($errors->has('xCollocazione')) <p class="help-block">{{ $errors->first('xCollocazione') }}</p> @endif -->
      </div>

    <div class="form-group">
      <!-- <input class="col-sm-2 control-label btn btn-default"  name="biblioteca" type="submit" value="CERCA" > -->
      <button class="btn btn-success"   name="rtn"  type="submit">inserimento</button>
    </div>

    </form>

    @if ( $errors->any())
           <div class="alert alert-danger">
               @foreach ($errors->all() as $error)
                   {{ $error }}<br>
               @endforeach
           </div>
   @endif

  </div>
@endsection
