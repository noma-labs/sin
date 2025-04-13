<x-modal
    modal-title="Sposta Persona in Azienda"
    button-title="Sposta"
    button-style="btn-warning my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="spostaPersonAzienda{{ $lavoratore->id }}"
            action="{{ route("nomadelfia.aziende.persona.sposta", ["id" => $azienda->id, "idPersona" => $lavoratore->id]) }}"
        >
            @method("PUT")
            @csrf

            <div class="row">
                <label for="inputData" class="col-sm-6 form-label">
                    Data fine
                </label>
                <div class="col-sm-6">
                    <input
                        type="date"
                        name="data_fine"
                        value="{{ old("data_fine") }}"
                        class="form-control"
                    />
                </div>
            </div>
            <hr />

            <div class="row">
                <label for="staticEmail" class="col-sm-6 form-label">
                    Nuova azienda
                </label>
                <div class="col-sm-6">
                    <select name="nuova_azienda_id" class="form-select">
                        <option selected>---seleziona azienda ---</option>
                        @foreach (App\Nomadelfia\Azienda\Models\Azienda::orderBy("nome_azienda")->get() as $a)
                            @if ($a->id != $azienda->id)
                                <option
                                    value="{{ $a->id }}"
                                    {{ old("nuova_azienda_id") == $a->id ? "selected" : "" }}
                                >
                                    {{ $a->nome_azienda }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button
            class="btn btn-success"
            form="spostaPersonAzienda{{ $lavoratore->id }}"
        >
            Salva
        </button>
    </x-slot>
</x-modal>
