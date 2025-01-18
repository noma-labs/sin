<my-modal
    modal-title="Aggiungi Anno"
    button-title="Aggiungi Anno"
    button-style="btn-primary my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="addClasse"
            action="{{ route("scuola.anno.aggiungi") }}"
        >
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Data Inizio
                </label>
                <div class="col-md-8">
                    <input
                        type="date"
                        name="data_inizio"
                        value="{{ old("data_inizio") }}"
                        class="form-control"
                    />
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button class="btn btn-danger" form="addClasse">Aggiungi</button>
    </template>
</my-modal>
