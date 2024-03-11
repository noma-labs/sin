<my-modal
    modal-title="Modifica data entrata"
    button-title="Modifica Entrata"
    button-style="btn-warning my-1"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formModificaDataEntrata{{ $persona->id }}"
            action="{{ route("nomadelfia.persone.dataentrata.modifica", ["idPersona" => $persona->id, "entrata" => $data_entrata]) }}"
        >
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-6 col-form-label">
                    Nuova Data Entrata
                </label>
                <div class="col-sm-6">
                    <label class="form-check-label">
                        <date-picker
                            :bootstrap-styling="true"
                            :typeable="true"
                            value="{{ $data_entrata }}"
                            format="yyyy-MM-dd"
                            name="data_entrata"
                        ></date-picker>
                    </label>
                    <small id="help" class="form-text text-muted">
                        Lasciare vuoto se coincide con la data di nascita della
                        persona.
                    </small>
                </div>
            </div>
        </form>
    </template>

    <template slot="modal-button">
        <button
            class="btn btn-success"
            form="formModificaDataEntrata{{ $persona->id }}"
        >
            Salva
        </button>
    </template>
</my-modal>
