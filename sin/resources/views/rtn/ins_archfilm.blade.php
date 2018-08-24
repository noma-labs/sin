@extends('layouts.app')

@section('title', 'rtn')

@section("archivioname","Inserimento film")

@section('archivio')
  <div class="list-group">
   <form method="POST" class="form-inline" action="/rtn/film">
       {{ csrf_field() }}
       <div class="form-group">
         <label for="xCategoria" class="control-label">CATEGORIA/NUMERO </label>
          <select class="form-control"   name="xCategoria" type="text" id="xCategoria">
           @foreach ($categorie as $categoria)
              <option> {{ $categoria->Series}}-{{ $categoria->numeros}}</option>
            @endforeach
           </select>
          <!-- @if ($errors->has('xCollocazione')) <p class="help-block">{{ $errors->first('xCollocazione') }}</p> @endif -->
      </div>

       <!-- <div class="form-group @if ($errors->has('xTitolo')) has-error @endif"> -->
       <div class="form-group">
         <label for="xRecord" class=" control-label" >Record </label>
         <select class="form-control"   name="xRecord" type="text" id="xRecord">
            @foreach ($records as $record)
              <option> {{ $record }}</option>
            @endforeach
           </select>
        <!-- @if ($errors->has('xTitolo')) <p class="help-block">{{ $errors->first('xTitolo') }}</p> @endif -->
      </div>

     <div class="form-group">
       <label for="xInizioMinuti" class="control-label" >Inizio Minuti</label>
       <input class="form-control" name="xInizioMinuti" type="text" id="xInizioMinuti" size="3" maxlength="10" placeholder="Ins. Inizio Minuti" />
      </div>
      
      <div class="form-group">
       <label for="xFineMinuti" class="control-label" >Fine Minuti</label>
       <input class="form-control" name="xFineMinuti" type="text" id="xFineMinuti" size="3" maxlength="10" placeholder="Ins. Fine Minuti" />
      </div>

     <div class="form-group">
      <label for="xDataRegistrazione" class="control-label">Data Registrazione</label>
      <input class="form-control" name="xDataRegistrazione" type="text" id="xDataRegistrazione" size="10" maxlength="10"  />
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

  </div>
@endsection
