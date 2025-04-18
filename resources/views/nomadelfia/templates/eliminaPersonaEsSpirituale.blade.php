<x-modal
    modal-title="Elimina persona da es.spirituale"
    button-title="Elimina"
    button-style="btn-danger my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formEliminaFromEsSpirituale{{ $posizione->id }}"
            action="{{ route("nomadelfia.person.position.delete", ["id" => $persona->id, "idPos" => $posizione->id]) }}"
        >
            @csrf
            @method("delete")
            <body>
                Vuoi davvero eliminare {{ $persona->nominativo }} dalla
                posizione {{ $posizione->nome }} ?
            </body>
        </form>
    </x-slot>
    <x-slot:footer>
        <button
            class="btn btn-danger"
            form="formEliminaFromEsSpirituale{{ $posizione->id }}"
        >
            Elimina
        </button>
    </x-slot>
</x-modal>
