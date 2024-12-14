<my-modal
    modal-title="Clona anno"
    button-title="Clona anno"
    button-style="btn-info my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="cloneAnno"
            action="{{ route("scuola.anno.clone", ["id" => $anno->id]) }}"
        >
            {{ csrf_field() }}
            <div class="alert alert-info" role="alert">
                Gli studenti verranno importati in un nuovo anno facendoli avanzare di una classe.
            </div>
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Data Inizio
                </label>
                <div class="col-md-8">
                    <date-picker
                        :bootstrap-styling="true"
                        value="{{ old("anno_inizio") }}"
                        format="yyyy-MM-dd"
                        name="anno_inizio"
                    ></date-picker>
                </div>
            </div>
        </form>
    </template>
    s
    <template slot="modal-button">
        <button class="btn btn-danger" form="cloneAnno">Crea</button>
    </template>
</my-modal>
