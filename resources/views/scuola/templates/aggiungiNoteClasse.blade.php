<x-modal
    modal-title="Note Classe"
    button-title="Aggiorna Note"
    button-style="btn-warning"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="addNote"
            action="{{ route("scuola.classi.note.update", ["id" => $classe->id]) }}"
        >
            @method("PUT")
            @csrf
            <div class="mb-3">
                <label class="form-label">Not sdfasdfasgfe</label>
                <textarea
                    class="form-control"
                    name="note"
                    aria-label="With textarea"
                >
                    {{ $classe->note }}
                </textarea>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-danger btn-sm" form="addNote">Salva</button>
    </x-slot>
</x-modal>
