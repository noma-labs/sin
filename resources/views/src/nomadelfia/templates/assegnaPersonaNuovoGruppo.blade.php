<x-modal
    modal-title="Assegna Gruppo Familiare"
    button-title="Nuovo Gruppo"
    button-style="btn-success my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formPersonaGruppo"
            action="{{ route("nomadelfia.persone.gruppo.assegna", ["idPersona" => $persona->id]) }}"
        >
            @csrf

            <h5 class="my-2">Nuovo gruppo familiare</h5>
            <div class="mb-3 row">
                <label for="staticEmail" class="col-sm-6 col-form-label">
                    Gruppo familiare
                </label>
                <div class="col-sm-6">
                    <select name="gruppo_id" class="form-select">
                        <option selected>---seleziona gruppo ---</option>
                        @foreach (Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare::all() as $gruppofam)
                            <option
                                value="{{ $gruppofam->id }}"
                                {{ old("gruppo_id") == $gruppofam->id ? "selected" : "" }}
                            >
                                {{ $gruppofam->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-6 col-form-label">
                    Data entrata gruppo familiare
                </label>
                <div class="col-sm-6">
                    <input
                        type="date"
                        name="data_entrata"
                        value="{{ old("data_entrata") }}"
                        class="form-control"
                    />
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-success" form="formPersonaGruppo">Salva</button>
    </x-slot>
</x-modal>
<!--end modal-->
