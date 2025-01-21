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
            action="{{ route("nomadelfia.famiglia.aggiorna", ["id" => $famiglia->id]) }}"
        >
            {{ csrf_field() }}
            <div class="mb-3 row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Nome famiglia
                </label>
                <div class="col-8">
                    <input
                        class="form-control"
                        type="text"
                        name="nome_famiglia"
                        value="{{ $famiglia->nome_famiglia }}"
                    />
                    {{ $famiglia->nome_famiglia }}
                </div>
            </div>
            <div class="mb-3 row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Data creazione famiglia:
                </label>
                <div class="col-8">
                    <input
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
