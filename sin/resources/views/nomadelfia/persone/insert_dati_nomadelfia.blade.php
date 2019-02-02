@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Inserimento dati Nomadelfia '. $persona->nominativo])

<div class="row">
  <!-- dati Nomadelfia -->
  <div class="col-md-6 offset-md-4">
    <h4> Categoria persona</h4>
    
    <div class="form-group row">
      <label class="col-sm-4 col-form-label">Categoria:</label>
      <div class="col-sm-8">
        <div class="form-group">
            <select class="form-control"  name="categoria_id">
              <option value="" selected>---seleziona categoria---</option>
              @foreach (App\Nomadelfia\Models\Categoria::all() as $cat)
                @if(old('categoria') == $cat->id)
                <option value="{{$cat->id}}" selected> {{ $cat->nome}}</option>
                @else
                <option value="{{$cat->id}}"> <p class="font-weight-bold"> {{ $cat->nome}}</span> ({{ $cat->descrizione}})</option>
                @endif
              @endforeach
          </select>
        </div>
      </div>
    </div> 
    <!-- <div class="form-group row">
      <label class="col-sm-4 col-form-label">Data inizio:</label>
      <div class="col-sm-8">
        <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') ? old('data_inizio'): Carbon::now()->toDateString() }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
      </div>
    </div>  -->
    <div class="form-group row">
      <label for="staticEmail" class="col-sm-4 col-form-label">Stato familiare</label>
      <div class="col-sm-8">
        <select name="stato_id" class="form-control">
            <option selecte>---Seleziona stato---</option>
            @foreach (App\Nomadelfia\Models\Stato::all() as $stato)
              <option value="{{$stato->id}}">{{$stato->nome}}</option>
            @endforeach
        </select>
      </div>
    </div>
    <div class="form-group row">
     <label for="inputState" class="col-sm-4 col-form-label">Posizione in Nomadelfia</label>
     <div class="col-sm-8">
      <select id="inputState" class="form-control" name="posizione_id">
        <option selected hidden value="">---Seleziona posizione---</option>
        @foreach(App\Nomadelfia\Models\Posizione::all() as $posizione)
        <option value="{{ $posizione->id }}" @if(old('posizione_id') === $posizione->id) selected @endif>{{ $posizione->nome }}</option>
        @endforeach
      </select>
      </div>
    </div>
    <div class="form-group row">
      <label for="gruppo" class="col-sm-4 col-form-label">Gruppo Familiare</label>
      <div class="col-sm-8">
        <select class="form-control" id="gruppo" name="gruppo_id">
          <option selected hidden value="">---Seleziona gruppo fmiliare---</option>
          @foreach(App\Nomadelfia\Models\GruppoFamiliare::all()  as $gruppo)
            <option value="{{ $gruppo->id }}" @if(old('gruppo_id')=== $gruppo->id) selected @endif>{{ $gruppo->nome }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-group row">
      <label for="azienda"  class="col-sm-4 col-form-label">Azienda</label>
      <div class="form-group col-md-8">
        <select class="form-control" id="azienda" name="azienda_id">
          <option value="" selected>---Seleziona azienda</option>
          @foreach(App\Nomadelfia\Models\Azienda::all() as $azienda)
          <option value="{{ $azienda->id }}" @if(old('azienda_id') === $azienda->id) selected @endif>{{ strtoupper($azienda->nome_azienda) }}</option>
          @endforeach
        </select>
      </div>  
    </div>
  </div>   <!-- end col dati Nomadelfia -->
   <!-- end col dati famiglia -->
</div>

<form method="POST" action="{{route('nomadelfia.persone.inserimento.datinomadelfia.confirm',['idPersona'=>$persona->id])}}">
{{ csrf_field() }}  
  <div class="row offset-md-4">
    <button class="btn btn-success" type="submit">Salva e aggiungi famiglia</button> 
  </div>
</form>




@endsection