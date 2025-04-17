<x-modal
    modal-title="Modifica Posizione attuale"
    button-title="Modifica"
    button-style="btn-warning my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formPersonaPosizioneModifica{{ $persona->id }}"
            action="{{ route("nomadelfia.person.position.update", ["id" => $persona->id, "idPos" => $id]) }}"
        >
            @method("PUT")
            @csrf
            <input
                type="hidden"
                name="current_data_inizio"
                value="{{ $data_inizio }}"
            />
            <div class="row">
                <label class="col-sm-6 form-label">Data Attuale</label>
                <div class="form-check col-sm-6">
                    <label class="form-check-label" for="exampleRadios1">
                        {{ $data_inizio }}
                    </label>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-6 form-label">Nuova Data</label>
                <div class="form-check col-sm-6">
                    <label class="form-check-label">
                        <input
                            type="date"
                            name="new_data_inizio"
                            value="{{ $persona->data_nascita }}"
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
            form="formPersonaPosizioneModifica{{ $persona->id }}"
        >
            Salva
        </button>
    </x-slot>
</x-modal>
<!--end modal modifica posizione-->
