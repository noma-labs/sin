<x-modal
    modal-title="Aggiungi Lavoratore"
    button-title="Aggiungi Lavoratore"
    button-style="btn-primary my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formAssegnaPersonaAzienda"
            action="{{ route("nomadelfia.azienda.lavoratore.assegna", ["id" => $azienda->id]) }}"
        >
            @csrf
            <div class="row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Persona
                </label>
                <div class="col-8">
                    <livewire:search-popolazione name_input="persona_id" />
                </div>
            </div>
            <div class="row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Data Inizio
                </label>
                <div class="col-8">
                    <input
                        type="date"
                        name="data_inizio"
                        value="{{ old("data_inizio") }}"
                        class="form-control"
                    />
                    <small id="emailHelp" class="form-text text-muted">
                        Lasciare vuoto se concide con la data di oggi.
                    </small>
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-danger" form="formAssegnaPersonaAzienda">
            Salva
        </button>
    </x-slot>
</x-modal>
