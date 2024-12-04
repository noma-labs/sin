<my-modal
    modal-title="Elimina Incarico"
    button-title="Elimina"
    button-style="btn-danger my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formEliminacaIncarico{{ $incarico->id }}"
            action="{{ route("nomadelfia.incarichi.delete", ["id" => $incarico->id]) }}"
        >
            @csrf
            @method("delete")
            <p>Vuoi davvero eliminare l'incarico {{ $incarico->nome }} ?</p>
            <small>
                L'incarico verrà eliminato e tutte le persone ad esso associate
                sarannao rimosse dall'incarico
            </small>
        </form>
    </template>
    <template slot="modal-button">
        <button
            class="btn btn-danger"
            form="formEliminacaIncarico{{ $incarico->id }}"
        >
            Elimina
        </button>
    </template>
</my-modal>
