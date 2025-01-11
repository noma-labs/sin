<my-modal
    modal-title="Modifica Persona in Azienda"
    button-title="Modifica"
    button-style="btn-warning my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="editPersonaAzienda{{ $lavoratore->id }}"
            action="{{ route("nomadelfia.aziende.persona.update", ["id" => $azienda->id, "idPersona" => $lavoratore->id]) }}"
        >
            @method("PUT")
            {{ csrf_field() }}

            <input
                type="hidden"
                name="data_inizio"
                value="{{ $lavoratore->pivot->data_inizio_azienda }}"
            />

            <div class="form-group row">
                <label for="inputData" class="col-sm-6 col-form-label">
                    Mansione
                </label>
                <div class="col-sm-6">
                    <select name="mansione" class="form-control">
                        <option selected>---seleziona mansione ---</option>
                        <option
                            value="LAVORATORE"
                            {{ $lavoratore->pivot->mansione == "LAVORATORE" ? "selected" : "" }}
                        >
                            LAVORATORE
                        </option>
                        <option
                            value="RESPONSABILE AZIENDA"
                            {{ $lavoratore->pivot->mansione == "RESPONSABILE AZIENDA" ? "selected" : "" }}
                        >
                            RESPONSABILE AZIENDA
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="inputData" class="col-sm-6 col-form-label">
                    Data inizio
                </label>
                <div class="col-sm-6">
                    <input
                        type="date"
                        name="nuova_data_inizio"
                        value="{{ $lavoratore->pivot->data_inizio_azienda }}"
                    />
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button
            class="btn btn-success"
            form="editPersonaAzienda{{ $lavoratore->id }}"
        >
            Salva
        </button>
    </template>
</my-modal>
