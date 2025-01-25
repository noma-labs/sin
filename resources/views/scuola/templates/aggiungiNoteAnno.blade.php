<x-modal
    modal-title="Note Anno Scolastico"
    button-title="Aggiorna Note"
    button-style="btn-warning"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="addNote"
            action="{{ route("scuola.anno.note.update", ["id" => $anno->id]) }}"
        >
            @method("PUT")
            @csrf
            <label class="form-label">Note</label>
            <textarea
                class="form-control"
                name="note"
                aria-label="With textarea"
            >{{$anno->descrizione }} </textarea >
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-danger btn-sm" form="addNote">Salva</button>
    </x-slot>
</x-modal>
