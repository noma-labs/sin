<my-modal
    modal-title="Aggiungi Lavoratore"
    button-title="Aggiungi Lavoratore"
    button-style="btn-primary my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formAssegnaPersonaAzienda"
            action="{{ route("nomadelfia.azienda.lavoratore.assegna", ["id" => $azienda->id]) }}"
        >
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Persona
                </label>
                <div class="col-8">
                    <livewire:search-popolazione name_input="persona_id" />
                </div>
            </div>
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Data Inizio
                </label>
                <div class="col-8">
                    <input type="date" name="data_inizio"  value="{{ old("data_inizio") }}" class="form-control" />
                    <small id="emailHelp" class="form-text text-muted">
                        Lasciare vuoto se concide con la data di oggi.
                    </small>
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button class="btn btn-danger" form="formAssegnaPersonaAzienda">
            Salva
        </button>
    </template>
</my-modal>
