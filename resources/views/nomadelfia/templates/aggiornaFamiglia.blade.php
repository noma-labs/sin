<x-modal
    modal-title="Aggiorna famiglia"
    button-title="Modifica"
    button-style="btn-warning my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formAggiornaFamiglia{{ $famiglia->id }}"
            action="{{ route("nomadelfia.families.update", $famiglia->id) }}"
        >
            @csrf
            @method("PUT")
            <div class="row mb-3">
                <label for="example-text-input" class="col-4 form-label">
                    Nome famiglia
                </label>
                <div class="col-8">
                    <input
                        class="form-control"
                        type="text"
                        name="nome_famiglia"
                        value="{{ $famiglia->nome_famiglia }}"
                    />
                </div>
            </div>
            <div class="row">
                <label for="example-text-input" class="col-4 form-label">
                    Data creazione famiglia:
                </label>
                <div class="col-8">
                    <input
                        class="form-control"
                        type="date"
                        name="data_creazione"
                        value="{{ $famiglia->data_creazione }}"
                    />
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button
            class="btn btn-danger"
            form="formAggiornaFamiglia{{ $famiglia->id }}"
        >
            Salva
        </button>
    </x-slot>
</x-modal>
