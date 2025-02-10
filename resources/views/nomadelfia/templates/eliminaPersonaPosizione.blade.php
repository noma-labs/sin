<x-modal
    modal-title="Elimina posizione della persona"
    button-title="Elimina"
    button-style="btn-danger my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formEliminaposizioneStorico{{ $posizione->id }}"
            action="{{ route("nomadelfia.persone.posizione.elimina", ["idPersona" => $persona->id, "id" => $posizione->id]) }}"
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
            form="formEliminaposizioneStorico{{ $persona->id }}"
        >
            Elimina
        </button>
    </x-slot>
</x-modal>
