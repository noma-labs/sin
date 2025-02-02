<x-modal
    modal-title="Aggiungi Persona incarico"
    button-title="Aggiungi Persona"
    button-style="btn-primary my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formAssegnaIncaricoPersona"
            action="{{ route("nomadelfia.incarichi.assegna", ["id" => $incarico->id]) }}"
        >
            @csrf
            <div class="row">
                <label for="example-text-input" class="col-4 form-label">
                    Persona
                </label>
                <div class="col-8">
                    <livewire:search-popolazione name_input="persona_id" />
                </div>
            </div>
            <div class="row">
                <label for="example-text-input" class="col-4 form-label">
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
        <button class="btn btn-danger" form="formAssegnaIncaricoPersona">
            Salva
        </button>
    </x-slot>
</x-modal>
