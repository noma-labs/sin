<x-modal
    modal-title="Modifica data entrata"
    button-title="Modifica Entrata"
    button-style="btn-warning my-1"
>
    <x-slot:body>
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
                        <input
                            type="date"
                            name="data_entrata"
                            value="{{ $data_entrata }}"
                            class="form-control"
                        />
                    </label>
                    <small id="help" class="form-text text-muted">
                        Lasciare vuoto se coincide con la data di nascita della
                        persona.
                    </small>
                </div>
            </div>
        </form>
    </x-slot>

    <x-slot:footer>
        <button
            class="btn btn-success"
            form="formModificaDataEntrata{{ $persona->id }}"
        >
            Salva
        </button>
    </x-slot>
</x-modal>
