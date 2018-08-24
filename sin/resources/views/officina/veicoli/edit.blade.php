@extends('officina.index')

@section('archivio')
@include('partials.header', ['title' => 'Modifica Veicolo'])
<form method="POST"  id="veicolo-form-modifica" action="{{route('veicoli.modifica.confirm', $veicolo->id)}}">
    {{ csrf_field() }}
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label>Targa</label>
            <input type="text" class="form-control" name="targa" value="{{$veicolo->targa}}">
         </div>
       </div>
       <div class="col-md-3">
          <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" class="form-control" name="nome" value="{{$veicolo->nome}}">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
             <label for="marca">Marca</label>
             <select class="form-control" name="marca_id" type="text">
               @foreach ($marche as $marca)
                 <option value="{{ $marca->id }}" @if($marca->id == $veicolo->modello->marca->id) selected @endif>{{ $marca->nome }}</option>
               @endforeach
             </select>
           </div>
         </div>

        <div class="col-md-3">
          <div class="form-group">
             <label for="modello">Modello</label>
             <input type="text" class="form-control" name="modello_id" value="{{$veicolo->modello->nome}}" disabled>
           </div>
         </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <label for="tipologia" >Tipologia</label>
            <select class="form-control" name="tipologia_id" type="text">
              @foreach ($tipologie as $tipologia)
                <option value="{{ $tipologia->id }}" @if($tipologia->id == $veicolo->tipologia_id) selected @endif>{{ $tipologia->nome }}</option>
              @endforeach
            </select>
           </div>
         </div>
        <div class="col-md-3">
          <div class="form-group">
              <label for="impiego">Impiego</label>
              <select class="form-control" name="impiego_id" type="text">
                @foreach ($impieghi as $impiego)
                  <option value="{{ $impiego->id }}" @if($impiego->id == $veicolo->impiego_id) selected @endif>{{ $impiego->nome }}</option>
                @endforeach
              </select>
             </div>
           </div>

         <div class="col-md-3">
           <div class="form-group">
             <label for="alimentazione">Alimentazione</label>
             <select class="form-control" name="alimentazione_id" type="text">
               @foreach ($alimentazioni as $alimentazione)
                 <option value="{{ $alimentazione->id }}" @if($alimentazione->id == $veicolo->alimentazione_id) selected @endif>{{ $alimentazione->nome }}</option>
               @endforeach
             </select>
           </div>
         </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="posti">N. Posti</label>
              <input type="text" class="form-control" name="num_posti" value="{{$veicolo->num_posti}}">
             </div>
           </div>
        </div>
        <div class="row">
          <div class="col-md-8">
          <div class="form-check">
           <label class="form-check-label">
             <input class="form-check-input" type="hidden"  name="prenotabile"  value="0"  >
             <input class="form-check-input" type="checkbox"  name="prenotabile"  value="1"   @if($veicolo->prenotabile) checked=checked @endif >
              Il veicolo è prenotabile
           </label>
          </div>
          </div>
        </div>
    </div>
  </div>
</form>

<button class="btn btn-primary"  form="veicolo-form-modifica"  type="submit">Salva</button>



<!-- end section dettagli prenotazione -->
@endsection
