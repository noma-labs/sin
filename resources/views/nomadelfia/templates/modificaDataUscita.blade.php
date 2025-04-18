<x-modal
    modal-title="Modifica data uscita"
    button-title="Modifica Uscita"
    button-style="btn-warning my-1"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formModificaDataUscita{{ $data_uscita }}"
            action="{{ route("nomadelfia.leave.update", ["id" => $persona->id, "uscita" => $data_uscita]) }}"
        >
            @csrf
            <div class="row">
                <label class="col-sm-6 form-label">Nuova Data Uscita</label>
                <div class="col-sm-6">
                    <label class="form-check-label">
                        <input
                            type="date"
                            name="data_uscita"
                            value="{{ $data_uscita }}"
                            class="form-control"
                        />
                    </label>
                </div>
            </div>
        </form>
    </x-slot>

    <x-slot:footer>
        <button
            class="btn btn-success"
            form="formModificaDataUscita{{ $data_uscita }}"
        >
            Salva
        </button>
    </x-slot>
</x-modal>
