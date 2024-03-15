<my-modal
    modal-title="Elimina persona dal Gruppo familiare "
    button-title="Elimina"
    button-style="btn-danger my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formEliminaGruppoStorico{{ $gruppo->id }}"
            action="{{ route("nomadelfia.persone.gruppo.elimina", ["idPersona" => $persona->id, "id" => $gruppo->id]) }}"
        >
            @csrf
            @method("delete")
            <body>
                Vuoi davvero eliminare {{ $persona->nominativo }} dal gruppo
                familiare {{ $gruppo->nome }} ?
            </body>
        </form>
    </template>
    <template slot="modal-button">
        <button
            class="btn btn-danger"
            form="formEliminaGruppoStorico{{ $gruppo->id }}"
        >
            Elimina
        </button>
    </template>
</my-modal>
