<my-modal
    modal-title="Aggiorna famiglia"
    button-title="Modifica"
    button-style="btn-warning my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formAggiornaFamiglia{{ $famiglia->id }}"
            action="{{ route("nomadelfia.famiglia.aggiorna", ["id" => $famiglia->id]) }}"
        >
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Nome famiglia
                </label>
                <div class="col-8">
                    <input
                        class="form-control"
                        type="text"
                        name="nome_famiglia"
                        value="{{ $famiglia->nome_famiglia }}"
                    />
                    {{ $famiglia->nome_famiglia }}
                </div>
            </div>
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Data creazione famiglia:
                </label>
                <div class="col-8">
                    <date-picker
                        :bootstrap-styling="true"
                        value="{{ $famiglia->data_creazione }}"
                        format="yyyy-MM-dd"
                        name="data_creazione"
                    ></date-picker>
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button
            class="btn btn-danger"
            form="formAggiornaFamiglia{{ $famiglia->id }}"
        >
            Salva
        </button>
    </template>
</my-modal>
