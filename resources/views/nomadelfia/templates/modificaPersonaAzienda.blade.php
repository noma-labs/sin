<x-modal
    modal-title="Modifica Persona in Azienda"
    button-title="Modifica"
    button-style="btn-warning my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="editPersonaAzienda{{ $lavoratore->id }}"
            action="{{ route("nomadelfia.aziende.persona.update", ["id" => $azienda->id, "idPersona" => $lavoratore->id]) }}"
        >
            @method("PUT")
            @csrf

            <input
                type="hidden"
                name="data_inizio"
                value="{{ $lavoratore->pivot->data_inizio_azienda }}"
            />

            <div class="mb-3">
                <label for="inputData" class="form-label">Mansione</label>
                <select name="mansione" class="form-select">
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

            <div class="mb-3">
                <label for="inputData" class="form-label">Data inizio</label>
                <input
                    type="date"
                    class="form-control"
                    name="nuova_data_inizio"
                    value="{{ $lavoratore->pivot->data_inizio_azienda }}"
                />
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button
            class="btn btn-success"
            form="editPersonaAzienda{{ $lavoratore->id }}"
        >
            Salva
        </button>
    </x-slot>
</x-modal>
