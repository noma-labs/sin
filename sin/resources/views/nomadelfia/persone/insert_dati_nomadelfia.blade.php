@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Inserimento dati Nomadelfia '. $persona->nominativo])



<form method="POST" action="{{route('nomadelfia.persone.inserimento.datinomadelfia.confirm',['idPersona'=>$persona->id])}}">
{{ csrf_field() }}  

<!-- categoria Nomadelfia -->
<div class="row justify-content-center">
    <div class="form-group col-md-3">
      <label for="categoria">Categoria</label>
      <select class="form-control"  id="categoria" name="categoria_id">
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
    <div class="form-group col-md-3">
      <label for="categoria_datainizio">Data inizio categoria</label>
      <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') ? old('data_inizio'): Carbon::now()->toDateString() }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
    </div>     
</div>

<!-- posizione nomadelfia -->
<div class="row justify-content-center">
    <div class="form-group col-md-3">
      <label for="posizione">Posizione Nomadelfia</label>
      <select id="posizione" class="form-control" name="posizione_id">
          <option selected hidden value="">---Seleziona posizione---</option>
          @foreach(App\Nomadelfia\Models\Posizione::all() as $posizione)
          <option value="{{ $posizione->id }}" @if(old('posizione_id') === $posizione->id) selected @endif>{{ $posizione->nome }}</option>
          @endforeach
      </select>
    </div>
    <div class="form-group col-md-3">
      <label for="categoria_datainizio">Data inizio posizione</label>
      <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') ? old('data_inizio'): Carbon::now()->toDateString() }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
    </div>     
</div>

<!-- stato familiare -->
<div class="row justify-content-center">
    <div class="form-group col-md-3">
      <label for="statofamiliare">Stato familiare</label>
      <select name="stato_id" class="form-control">
          <option selecte>---Seleziona stato familiare---</option>
          @foreach (App\Nomadelfia\Models\Stato::all() as $stato)
            <option value="{{$stato->id}}">{{$stato->nome}}</option>
          @endforeach
      </select>
    </div>
    <div class="form-group col-md-3">
      <label for="categoria_datainizio">Data inizio stato familiare</label>
      <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') ? old('data_inizio'): Carbon::now()->toDateString() }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
    </div>     
</div>

<!-- gruppo familiare -->
<div class="row justify-content-center">
    <div class="form-group col-md-3">
      <label for="gruppofamiliare">Gruppo familiare</label>
        <select class="form-control" id="gruppofamiliare" name="gruppo_id">
          <option selected hidden value="">---Seleziona gruppo familiare---</option>
          @foreach(App\Nomadelfia\Models\GruppoFamiliare::all()  as $gruppo)
            <option value="{{ $gruppo->id }}" @if(old('gruppo_id')=== $gruppo->id) selected @endif>{{ $gruppo->nome }}</option>
          @endforeach
        </select>
    </div>
    <div class="form-group col-md-3">
      <label for="categoria_datainizio">Data inizio gruppo familiare</label>
      <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') ? old('data_inizio'): Carbon::now()->toDateString() }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
    </div>     
</div>

<!-- azienda-->
<div class="row justify-content-center">
    <div class="form-group col-md-3">
      <label for="azienda">Azienda</label>
      <select class="form-control" id="azienda" name="azienda_id">
        <option value="" selected>---Seleziona azienda---</option>
        @foreach(App\Nomadelfia\Models\Azienda::all() as $azienda)
        <option value="{{ $azienda->id }}" @if(old('azienda_id') === $azienda->id) selected @endif>{{ strtoupper($azienda->nome_azienda) }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group col-md-3">
      <label for="categoria_datainizio">Data inizio azienda</label>
      <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') ? old('data_inizio'): Carbon::now()->toDateString() }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
    </div>     
</div>

<div class="row">
  <button class="btn btn-success offset-md-6" type="submit">Salva e aggiungi famiglia</button> 
</div>
</form>

<div class="row">
 <div class="col-md-4 offset-4">
  <div class="progress">
    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">2</div>
  </div>
 </div>

</div>
</div>






@endsection