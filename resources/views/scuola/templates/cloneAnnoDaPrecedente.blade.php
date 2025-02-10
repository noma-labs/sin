<x-modal
    modal-title="Clona anno"
    button-title="Clona anno"
    button-style="btn-warning my-2"
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
            <label class="form-label">Data Inizio</label>
            <input
                type="date"
                name="anno_inizio"
                value="{{ old("anno_inizio") }}"
                class="form-control"
            />
        </form>
    </x-slot>
    s
    <x-slot:footer>
        <button class="btn btn-danger" form="cloneAnno">Crea</button>
    </x-slot>
</x-modal>
