<form method="GET" action="{{ route("officina.ricerca.submit") }}">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <label class="form-label">Cliente</label>
            <select class="form-select" id="cliente" name="cliente_id">
                <option selected value>--Seleziona--</option>
                @foreach ($clienti as $cliente)
                    <option
                        value="{{ $cliente->id }}"
                        @if (old('cliente_id') !== null && old('cliente_id') === $cliente->id) selected @endif
                    >
                        {{ $cliente->nominativo }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label">Veicolo</label>
                <select class="form-select" id="veicolo" name="veicolo_id">
                    <option selected value>--Seleziona--</option>
                    @foreach ($veicoli as $veicolo)
                        <option value="{{ $veicolo->id }}">
                            {{ "(" . $veicolo->targa . ") " . $veicolo->nome . " - " . $veicolo->impiego->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label">Meccanico</label>
                <select class="form-select" id="meccanico" name="meccanico_id">
                    <option selected value>--Seleziona--</option>
                    @foreach ($meccanici as $meccanico)
                        <option value="{{ $meccanico->persona_id }}">
                            {{ $meccanico->nominativo }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="mb-3">
                <label for="uso">Uso</label>
                <select class="form-select" id="uso" name="uso_id" required>
                    <option disabled selected>--Seleziona--</option>
                    @foreach ($usi as $uso)
                        <option value="{{ $uso->ofus_iden }}">
                            {{ $uso->ofus_nome }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="mb-3">
                <label class="form-label">Data partenza</label>
                <select
                    class="form-select"
                    name="criterio_data_partenza"
                    type="text"
                >
                    <option value="<">Minore</option>
                    <option value="<=">Minore Uguale</option>
                    <option value="=">Uguale</option>
                    <option value=">">Maggiore</option>
                    <option value=">=" selected>Maggiore Uguale</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb-3">
                <label class="form-label">&nbsp;</label>
                <input type="date" class="form-control" name="data_partenza" />
            </div>
        </div>

        <div class="col-md-2">
            <div class="mb-3">
                <label class="form-label">Data arrivo</label>
                <select
                    class="form-select"
                    name="criterio_data_arrivo"
                    type="text"
                >
                    <option value="<">Minore</option>
                    <option value="<=" selected>Minore Uguale</option>
                    <option value="=">Uguale</option>
                    <option value=">">Maggiore</option>
                    <option value=">=">Maggiore Uguale</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <input
                type="date"
                class="form-control"
                id="data_arr"
                name="data_arrivo"
            />
        </div>

        <div class="col-md-4">
            <label class="form-label">Tutte le prenotazioni nel giorno:</label>
            <input
                type="date"
                class="form-control"
                id="data_arr"
                name="data_singola"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="mb-3">
                <label for="destinazione">Destinazione</label>
                <input
                    type="text"
                    class="form-control"
                    id="destinazione"
                    name="destinazione"
                />
            </div>
        </div>

        <div class="col-md-5">
            <div class="mb-3">
                <label for="note">Note</label>
                <input type="text" class="form-control" id="note" name="note" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb-3">
                <label id="lab">&nbsp;</label>
                <button type="submit" class="btn btn-primary">Ricerca</button>
            </div>
        </div>
    </div>
    <br />
</form>
