<x-modal
    modal-title="Rimuovi Alunno"
    button-title="Rimuovi"
    button-style="btn-danger my-1"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formRimuoviAlunno{{ $alunno->id }}"
            action="{{ route("scuola.classi.alunno.rimuovi", ["id" => $classe->id, "alunno_id" => $alunno->id]) }}"
        >
            @csrf
            <div class="mb-3 row">
                <p>
                    Voi davvero eliminare l'alunno {{ $alunno->nominativo }}
                    dalla {{ $classe->tipo->nome }} ?
                </p>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button
            class="btn btn-danger btn-sm"
            form="formRimuoviAlunno{{ $alunno->id }}"
        >
            Elimina
        </button>
    </x-slot>
</x-modal>
