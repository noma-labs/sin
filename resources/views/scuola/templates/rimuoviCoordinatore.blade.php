<my-modal
    modal-title="Rimuovi Coordinatore"
    button-title="Rimuovi"
    button-style="btn-danger my-1"
>
    <template slot="modal-body-slot">
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
    </template>
    \
    <template slot="modal-button">
        <button
            class="btn btn-danger btn-sm"
            form="formRimuoviCoordinatore{{ $coord->id }}"
        >
            Elimina
        </button>
    </template>
</my-modal>
