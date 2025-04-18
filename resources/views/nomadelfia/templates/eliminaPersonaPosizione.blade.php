<x-modal
    modal-title="Elimina posizione della persona"
    button-title="Elimina"
    button-style="btn-danger my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="deletePosition{{ $posizione->id }}"
            action="{{ route("nomadelfia.person.position.delete", ["id" => $persona->id, "idPos" => $posizione->id]) }}"
        >
            @csrf
            @method("DELETE")
            <body>
                Vuoi davvero eliminare {{ $persona->nominativo }} dalla
                posizione {{ $posizione->nome }} ?
            </body>
        </form>
    </x-slot>
    <x-slot:footer>
        <button
            class="btn btn-danger"
            form="deletePosition{{ $posizione->id }}"
            type="submit"
        >
            Elimina
        </button>
    </x-slot>
</x-modal>
