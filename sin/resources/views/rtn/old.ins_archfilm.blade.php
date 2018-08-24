
@extends('layouts.app')

@section('title', 'libri')

@section('archivioname', 'Ricerca Libri')

@section('archivio')

     <form method="POST" class="form-inline" action="/biblioteca/libri">
       {{ csrf_field() }}
       <div class="form-group">
         <label for="xCollocazione" class=" control-label"  >Collocazione </label>
         <input class="form-control" name="xCollocazione" type="text" id="xCollocazione" placeholder="Ins. Collocazione" >
          <!-- @if ($errors->has('xCollocazione')) <p class="help-block">{{ $errors->first('xCollocazione') }}</p> @endif -->
      </div>

       <!-- <div class="form-group @if ($errors->has('xTitolo')) has-error @endif"> -->
       <div class="form-group">
         <label for="xTitolo" class=" control-label" >Titolo </label>
        <input class="form-control" name="xTitolo" type="text" id="xTitolo"  placeholder="Inserisci Titolo Libro" >
        <!-- @if ($errors->has('xTitolo')) <p class="help-block">{{ $errors->first('xTitolo') }}</p> @endif -->
      </div>

     <div class="form-group">
       <label for="xAutore" class="control-label" >Autore</label>
        <input class="form-control" name="xAutore" type="text" id="xAutore" size="10" maxlength="10" placeholder="Ins. Autore" />
      </div>

     <div class="form-group">
      <label for="xEditore" class="control-label">Editore</label>
      <input class="form-control" name="xEditore" type="text" id="xEditore" size="10" maxlength="10" placeholder="Ins. Editore" />
    </div>

    <div class="form-group">
      <label for="xClassificazione"  class="control-label" >Classificazione </label>
           <select class="form-control"   name="xClassificazione" type="text" id="xClassificazione">
             <option value=""></option>
             @foreach ($classificazioni as $cls)
              <option value={{ $cls->descrizione}}> {{ $cls->descrizione}}</option>
            @endforeach
           </select>
     </div>

     <div class="form-group">
       <label for="xNote" class="control-label"  >Note </label>
       <input class="form-control" name="xNote" type="text" id="xNote" size="20" maxlength="100" placeholder="Ins. Parola da ricercare nelle note" />
     </div>

    <div class="form-group">
      <!-- <input class="col-sm-2 control-label btn btn-default"  name="biblioteca" type="submit" value="CERCA" > -->
      <button class="btn btn-success"   name="biblioteca"  type="submit">Cerca</button>
    </div>
    </form>

    @if ( $errors->any())
           <div class="alert alert-danger">
               @foreach ($errors->all() as $error)
                   {{ $error }}<br>
               @endforeach
           </div>
   @endif
@endsection
