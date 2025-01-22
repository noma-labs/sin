<x-modal
    modal-title="Clona anno"
    button-title="Clona anno"
    button-style="btn-info my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="cloneAnno"
            action="{{ route("scuola.anno.clone", ["id" => $anno->id]) }}"
        >
            @csrf
            <div class="alert alert-info" role="alert">
                Gli studenti verranno importati in un nuovo anno facendoli
                avanzare di una classe.
            </div>
            <div class="mb-3 row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Data Inizio
                </label>
                <div class="col-md-8">
                    <input
                        type="date"
                        name="anno_inizio"
                        value="{{ old("anno_inizio") }}"
                        class="form-control"
                    />
                </div>
            </div>
        </form>
    </x-slot>
    s
    <x-slot:footer>
        <button class="btn btn-danger" form="cloneAnno">Crea</button>
    </x-slot>
</x-modal>
