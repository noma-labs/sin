@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiungi Persona'])

<div class="row">
  <!-- Dati anagrafici -->
  <div class="col-md-4 offset-md-4">
    <h4>Dati Anagrafici</h4>
    <form method="POST" action="{{route('nomadelfia.persone.inserimento.anagrafici.confirm')}}">
      {{ csrf_field() }}
      <div class="form-group row">
        <label for="fornominativo" class="col-sm-6 col-form-label">Nominativo:</label>
        <div class="col-sm-6">
          <input class="form-control" id="fornominativo" name="nominativo"  value="{{ old('nominativo') }}" placeholder="Nominativo">
        </div>
      </div>
      <div class="form-group row">
        <label for="fornome" class="col-sm-6 col-form-label">Nome:</label>
        <div class="col-sm-6">
          <input  class="form-control" id="fornome" name="nome" value="{{ old('nome') }}" placeholder="Nome">
        </div>
      </div>
      <div class="form-group row">
        <label for="forcognome" class="col-sm-6 col-form-label">Cognome:</label>
        <div class="col-sm-6">
          <input type="text" class="form-control" id="forcognome" name="cognome" placeholder="Cognome" value="{{ old('cognome') }}">
        </div>
      </div>
      
      <div class="form-group row">
        <label for="fornascita" class="col-sm-6 col-form-label">Data di Nascita:</label>
        <div class="col-sm-6">
          <date-picker :bootstrap-styling="true" value="{{ old('data_nascita') }}" format="yyyy-MM-dd" name="data_nascita"></date-picker>
        </div>
      </div>
      <div class="form-group row">
        <label for="forluogo" class="col-sm-6 col-form-label">Luogo di nascita:</label>
        <div class="col-sm-6">
          <input  class="form-control" id="forluogo" placeholder="Luogo di nascita" name="luogo_nascita" value="{{ old('luogo_nascita') }}">
        </div>
      </div>
      <fieldset class="form-group" >
        <div class="row">
          <legend class="col-form-label col-sm-6 pt-0">Sesso:</legend>
          <div class="col-sm-6">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="sesso" id="forsessoM" value="M" @if(old('sesso')=='M') checked @endif>
                <label class="form-check-label" for="forsessoM">
                  Maschio
                </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="sesso" id="forsessoF"  value="M" @if(old('sesso')=='F') checked @endif>
              <label class="form-check-label" for="forsessoF">
                Femmina
              </label>
            </div>
          </div>
        </div>
      </fieldset>
      <div class="row my-2">
        <div class="col-auto">
          <!-- <button class="btn btn-warning " name="_addanother" value="true" type="submit">Salva e aggiungi un'altro </button> -->
          <button class="btn btn-success" name="_addonly" value="true" type="submit">Salva e aggiungi dati nomadelifia</button> 
        </div>
      </div>
      
    </form>
  </div>  <!-- end col dati anagrafici -->

  <!-- dati Nomadelfia -->
  <!-- <div class="col-md-4">
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

    <div class="form-group row">
      <label class="col-sm-4 col-form-label">Data inizio:</label>
      <div class="col-sm-8">
        <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') ? old('data_inizio'): Carbon::now()->toDateString() }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
      </div>
    </div> 

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
  </div>   -->
   <!-- end col dati Nomadelfia -->


<!--
  <div class="col-md-4">
  <h4>Dati Famiglia</h4>

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
  -->
   <!-- end col dati famiglia -->

</div>


@endsection