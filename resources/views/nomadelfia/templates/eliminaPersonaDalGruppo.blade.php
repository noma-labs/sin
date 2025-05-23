<x-modal
    modal-title="Elimina persona dal Gruppo familiare "
    button-title="Elimina"
    button-style="btn-danger my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formEliminaGruppoStorico{{ $gruppo->id }}"
            action="{{ route("nomadelfia.persone.gruppo.delete", ["id" => $persona->id, "idGruppo" => $gruppo->id]) }}"
        >
            @csrf
            @method("delete")
            <body>
                Vuoi davvero eliminare {{ $persona->nominativo }} dal gruppo
                familiare {{ $gruppo->nome }} ?
            </body>
        </form>
    </x-slot>
    <x-slot:footer>
        <button
            class="btn btn-danger"
            form="formEliminaGruppoStorico{{ $gruppo->id }}"
        >
            Elimina
        </button>
    </x-slot>
</x-modal>
