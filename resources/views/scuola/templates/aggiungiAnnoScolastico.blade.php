<x-modal
    modal-title="Aggiungi Anno"
    button-title="Aggiungi Anno"
    button-style="btn-primary my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="addClasse"
            action="{{ route("scuola.anno.aggiungi") }}"
        >
            @csrf
            <div class=" row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Data Inizio
                </label>
                <div class="col-md-8">
                    <input
                        type="date"
                        name="data_inizio"
                        value="{{ old("data_inizio") }}"
                        class="form-control"
                    />
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-danger" form="addClasse">Aggiungi</button>
    </x-slot>
</x-modal>
