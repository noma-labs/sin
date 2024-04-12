<div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label for="data_partenza">Data Partenza</label>
                <input
                    type="date"
                    class="form-control"
                    id="data_partenza"
                    name="data_par"
                    wire:model.live.debounce.250ms="dataPartenza"
                />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="ora_partenza">Ora Partenza</label>
                <input
                    type="time"
                    class="form-control"
                    id="ora_partenza"
                    name="ora_par"
                    wire:model.live.debounce.250ms="oraPartenza"
                    required
                />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="data_arrivo">Data Arrivo</label>
                <input
                    type="date"
                    class="form-control"
                    id="data_arrivo"
                    name="data_arr"
                    wire:model.live.debounce.250ms="dataArrivo"
                    required
                />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="ora_arrivo">Ora Arrivo</label>
                <input
                    type="time"
                    class="form-control"
                    id="ora_arrivo"
                    name="ora_arr"
                    wire:model.live.debounce.250ms="oraArrivo"
                    required
                />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="veicolo">Veicolo</label>
                <select class="form-control" name="veicolo">
                    <option selected disabled hidden>{{ $message }}</option>
                    @foreach ($veicoli as $impiego => $tipologie)
                        @foreach ($tipologie as $tipologia => $veicoli)
                            <optgroup
                                label="{{ucfirst($impiego) }} {{ $tipologia }}"
                            >
                                @foreach ($veicoli as $veicolo)
                                    <option
                                        value="{{ $veicolo->id }}"
                                        @if (! is_null($veicolo->prenotazione_id) && $veicolo->id != $selectedVeicolo)
                                            class="text-danger"
                                            disabled
                                        @endif
                                        @selected($veicolo->id == $selectedVeicolo)
                                    >
                                        {{ $veicolo->nome }}
                                        @if (! is_null($veicolo->prenotazione_id) && $veicolo->id != $selectedVeicolo)
                                            <span>
                                                {{ $veicolo->nominativo }}
                                                ({{ $veicolo->partenza }}
                                                {{ $veicolo->arrivo }})
                                            </span>
                                        @endif
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
