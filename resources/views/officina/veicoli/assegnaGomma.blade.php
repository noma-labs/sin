<my-modal
    modal-title="Assegna gomma al veicolo"
    button-title="Aggiungi"
    button-style="btn-block btn-warning"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="add-gomma"
            action="{{ route("veicoli.gomme.store", ["id" => $veicolo->id]) }}"
        >
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <label for="codice">Codice Gomma</label>
                    <select class="form-control" name="gomma_id">
                        <option value="" selected hidden>--Seleziona--</option>
                        @foreach ($gomme as $g)
                            <option value="{{ $g->id }}">
                                {{ $g->codice }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button type="submit" class="btn btn-danger" form="add-gomma">
            Salva
        </button>
    </template>
</my-modal>
