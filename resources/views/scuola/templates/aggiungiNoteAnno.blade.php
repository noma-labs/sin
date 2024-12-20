<my-modal
    modal-title="Note Anno Scolastico"
    button-title="Aggiorna Note"
    button-style="btn-warning"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="addNote"
            action="{{ route("scuola.anno.note.update", ["id" => $anno->id]) }}"
        >
            @method("PUT")
            @csrf
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Note</span>
                </div>
                <textarea
                    class="form-control"
                    name="note"
                    aria-label="With textarea"
                >
{{ $anno->descrizione }}</textarea
                >
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button class="btn btn-danger btn-sm" form="addNote">Salva</button>
    </template>
</my-modal>
