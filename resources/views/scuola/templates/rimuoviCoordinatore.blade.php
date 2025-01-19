<x-modal
    modal-title="Rimuovi Coordinatore"
    button-title="Rimuovi"
    button-style="btn-danger my-1"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formRimuoviCoordinatore{{ $coord->id }}"
            action="{{ route("scuola.classi.coordinatore.rimuovi", ["id" => $classe->id, "coord_id" => $coord->id]) }}"
        >
            {{ csrf_field() }}
            <div class="form-group row">
                <p>
                    Voi davvero eliminare {{ $coord->nominativo }} dalla
                    {{ $classe->tipo->nome }} ?
                </p>
            </div>
        </form>
    </x-slot>
    \
    <x-slot:footer>
        <button
            class="btn btn-danger btn-sm"
            form="formRimuoviCoordinatore{{ $coord->id }}"
        >
            Elimina
        </button>
    </x-slot>
</x-modal>
