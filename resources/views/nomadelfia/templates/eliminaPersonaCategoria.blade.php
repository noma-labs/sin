<my-modal
    modal-title="Elimina categoria della persona"
    button-title="Elimina"
    button-style="btn-danger my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formEliminacategoriaStorico{{ $categoria->id }}"
            action="{{ route("nomadelfia.persone.categoria.elimina", ["idPersona" => $persona->id, "id" => $categoria->id]) }}"
        >
            @csrf
            @method("delete")
            <body>
                Vuoi davvero eliminare {{ $persona->nominativo }} dal categoria
                familiare {{ $categoria->nome }} ?
            </body>
        </form>
    </template>
    <template slot="modal-button">
        <button
            class="btn btn-danger"
            form="formEliminacategoriaStorico{{ $categoria->id }}"
        >
            Elimina
        </button>
    </template>
</my-modal>
