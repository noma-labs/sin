<div>
    <form class="mb-3" wire:submit.prevent="saveReservation">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-md-2 col-6">
                <label class="form-label" for="data_partenza">Data Partenza</label>
                <input class="form-control" id="data_partenza" name="data_par" type="date"
                       wire:model.live.debounce.250ms="dataPartenza" />
                @error('dataPartenza')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label" for="ora_partenza">Ora Partenza</label>
                <input class="form-control" id="ora_partenza" name="ora_par" required type="time"
                       wire:model.live.debounce.250ms="oraPartenza" />
                @error('oraPartenza')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label" for="data_arrivo">Data Arrivo</label>
                <input class="form-control" id="data_arrivo" name="data_arr" required type="date"
                       wire:model.live.debounce.250ms="dataArrivo" />
                @error('dataArrivo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label" for="ora_arrivo">Ora Arrivo</label>
                <input class="form-control" id="ora_arrivo" name="ora_arr" required type="time"
                       wire:model.live.debounce.250ms="oraArrivo" />
                @error('oraArrivo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="veicolo">Veicolo</label>
                <select class="form-select" id="veiclo" name="veicolo" wire:model.live.debounce.250ms="selectedVeicolo">
                    <option hidden value>{{ $veicoloSelectPlaceholder }}</option>
                    @foreach ($veicoli as $impiego => $tipologie)
                        @foreach ($tipologie as $tipologia => $veicoli)
                            <optgroup label="{{ ucfirst($impiego) }} {{ $tipologia }} ({{ count($veicoli) }})">
                                @foreach ($veicoli as $veicolo)
                                    <option @if (!is_null($veicolo->prenotazione_id) && $veicolo->id != $selectedVeicolo) class="text-danger"
                                        disabled @endif
                                        value="{{ $veicolo->id }}">
                                        {{ $veicolo->nome }}
                                        @if (!is_null($veicolo->prenotazione_id) && $veicolo->id != $selectedVeicolo)
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
                @error('selectedVeicolo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-2">
                <label class="form-label" for="person">Cliente</label>
                <select class="form-select" id="person" name="nome" wire:model.live.debounce.250ms="selectedCliente">
                    <option hidden value>--Seleziona--</option>
                    @foreach ($clienti as $cliente)
                        <option value="{{ $cliente->id }}">
                            {{ $cliente->nominativo }}
                        </option>
                    @endforeach
                </select>
                @error('selectedCliente')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-2">
                <label class="form-label" for="meccanico">Meccanico</label>
                <select class="form-select" id="meccanico" name="meccanico" required wire:model.live.debounce.250ms="selectedMeccanico">
                    <option hidden value>--Seleziona--</option>
                    @foreach ($meccanici as $mecc)
                        <option @if (strtolower($mecc->nominativo) == 'gennaro' or strtolower($mecc->nominativo) == 'carlo s.') disabled @endif value="{{ $mecc->persona_id }}">
                            {{ $mecc->nominativo }}
                        </option>
                    @endforeach
                </select>
                @error('selectedMeccanico')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-2">
                <label class="form-label" for="uso">Uso</label>
                <select class="form-select" id="uso" name="uso" required wire:model.live.debounce.250ms="selectedUso">
                    <option hidden value>--Seleziona--</option>
                    @foreach ($usi as $uso)
                        <option value="{{ $uso->ofus_iden }}">
                            {{ $uso->ofus_nome }}
                        </option>
                    @endforeach
                </select>
                @error('selectedUso')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-2">
                <label class="form-label" for="destinazione">
                    Destinazione
                </label>
                <input class="form-control" id="destinazione" name="destinazione" type="text" value="Grosseto" wire:model.live.debounce.250ms="destinazione" placeholder="Inserisci"/>
                @error('destinazione')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="note">Note</label>
                <input class="form-control" id="note" name="note" type="text" wire:model.live.debounce.250ms="note" placeholder="Inserisci"/>
                @error('note')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col d-flex align-items-end">
                <button class="btn btn-primary ms-auto" id="prenota" type="submit">
                    Prenota
                </button>
            </div>
        </div>
    </form>
</div>
