<div>
  <div class="row">
    <div class="col-md-2">
      <div class="form-group">
        <label for="data_par">Data Partenza</label>
        <input
          type="date"
          class="form-control"
          id="data_par"
          name="data_par"
          wire:model="dataPartenza"
          required
        />
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <label for="ora_par">Ora Partenza</label>
        <input
          type="time"
          class="form-control"
          id="ora_par"
          name="ora_par"
          wire:model="oraPartenza"
          required
        />
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <label for="data_arr">Data Arrivo</label>
        <input
          type="date"
          class="form-control"
          id="data_arr"
          name="data_arr"
          wire:model="dataArrivo"
          required
        />
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <label for="ora_arr">Ora Arrivo</label>
        <input
          type="time"
          class="form-control"
          id="ora_arr"
          name="ora_arr"
          wire:model="oraArrivo"
          required
        />
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label for="veicolo">Veicolo</label>
        <select class="form-control" name="veicolo" >

            <option selected disabled hidden>--Seleziona veicolo--</option>
            @foreach ($veicoli as $impiego => $tipologie)
                @foreach ($tipologie as $tipologia => $veicoli)
                    <optgroup label="{{ $impiego }} - {{ $tipologia }}">
                        @foreach ($veicoli as $veicolo)

                            <option  @disabled(!is_null($veicolo->prenotazione_id)) value="{{$veicolo->id}}">
                                {{ $veicolo->nome }}
                                @isset($veicolo->prenotazione_id)
                                 {{ $veicolo->nominativo }} ({{ $veicolo->partenza }} {{$veicolo->arrivo}})
                                 @endisset
                            </option>
                        @endforeach
                    </optgroup>
                    @endforeach
            @endforeach
            </select>
        </select>
        </select>
      </div>
    </div>
  </div>
</div>
