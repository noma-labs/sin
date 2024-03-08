<my-modal
    modal-title="Modifica data uscita"
    button-title="Modifica Uscita"
    button-style="btn-warning my-1"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formModificaDataUscita{{ $data_uscita }}"
            action="{{ route("nomadelfia.persone.datauscita.modifica", ["idPersona" => $persona->id, "uscita" => $data_uscita]) }}"
        >
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-6 col-form-label">Nuova Data Uscita</label>
                <div class="col-sm-6">
                    <label class="form-check-label">
                        <date-picker
                            :bootstrap-styling="true"
                            :typeable="true"
                            value="{{ $data_uscita }}"
                            format="yyyy-MM-dd"
                            name="data_uscita"
                        ></date-picker>
                    </label>
                </div>
            </div>
        </form>
    </template>

    <template slot="modal-button">
        <button
            class="btn btn-success"
            form="formModificaDataUscita{{ $data_uscita }}"
        >
            Salva
        </button>
    </template>
</my-modal>
