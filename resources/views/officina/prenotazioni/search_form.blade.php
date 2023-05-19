<form  method="GET" action="{{ route('officina.ricerca.submit') }}">
   {{ csrf_field() }}
  <div class="row">
      <div class="col-md-3">
         <label class="control-label">Cliente </label>
          <select class="form-control" id="cliente" name="cliente_id">
           @foreach ($clienti as $cliente)
             <option value="{{ $cliente->id }}"  @if (old('cliente_id') == $cliente->id) selected @endif > {{ $cliente->nominativo }} </option>
           @endforeach
         </select>
      </div>

    <div class="col-md-3">
      <div class="form-group">
        <label class="control-label">Veicolo </label>
          <autocomplete placeholder="Inserisci veicolo o targa..." name="veicolo_id" url={{route('api.officina.veicoli.search')}}></autocomplete>
      </div>
    </div>

    <div class="col-md-3">
      <div class="form-group">
          <label class="control-label">Meccanico </label>
          <autocomplete placeholder="Inserisci nome..." name="meccanico_id" url={{route('api.officina.meccanici')}}></autocomplete>
      </div>
    </div>

    <div class="col-md-3">
      <div class="form-group">
        <label for="uso">Uso</label>
        <select class="form-control" id="uso" name="uso_id" required>
          <option disabled selected>--Seleziona--</option>
          @foreach ($usi as $uso)
          <option value="{{ $uso->ofus_iden }}">{{ $uso->ofus_nome }}</option>
          @endforeach
      </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2">
      <div class="form-group">
       <label class="control-label">Data partenza</label>
       <select class="form-control"  name="criterio_data_partenza" type="text" >
           <option value="<">Minore</option>
           <option value="<=">Minore Uguale</option>
           <option value="=" >Uguale</option>
           <option value=">">Maggiore</option>
           <option value=">=" selected>Maggiore Uguale</option>
       </select>
      </div>
      </div>
    <div class="col-md-2">
      <div class="form-group">
        <label >&nbsp;</label>
        <input type="date" class="form-control" name="data_partenza"  >
      </div>
    </div>

    <div class="col-md-2">
      <div class="form-group">
        <label class="control-label">Data arrivo</label>
        <select class="form-control"  name="criterio_data_arrivo" type="text" >
            <option value="<">Minore</option>
            <option value="<=" selected>Minore Uguale</option>
            <option value="=">Uguale</option>
            <option value=">">Maggiore</option>
            <option value=">=">Maggiore Uguale</option>
        </select>
      </div>
    </div>
    <div class="col-md-2">
      <label >&nbsp;</label>
      <input type="date" class="form-control" id="data_arr" name="data_arrivo" >
    </div>

    <div class="col-md-4">
      <label> Tutte le prenotazioni nel giorno: </label>
      <input type="date" class="form-control" id="data_arr" name="data_singola" >
    </div>


  </div>
  <div class="row">
    <div class="col-md-5">
      <div class="form-group">
        <label for="destinazione">Destinazione</label>
        <input type="text" class="form-control" id="destinazione" name="destinazione">
      </div>
    </div>

    <div class="col-md-5">
      <div class="form-group">
        <label for="note">Note</label>
        <input type="text" class="form-control" id="note" name="note">
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <label id="lab">&nbsp;</label>
        <button type="submit" class="btn btn-block btn-primary">Ricerca</button>
      </div>
    </div>
  </div>
  <br>
</form>
