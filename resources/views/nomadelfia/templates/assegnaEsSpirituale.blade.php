<my-modal
    modal-title="Assegna Es. Spirituali"
    button-title="Aggiungi Persona"
    button-style="btn-success my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="assegnaEsSpirituali{{ $esercizio->id }}"
            action="{{ route("nomadelfia.esercizi.assegna", ["id" => $esercizio->id]) }}"
        >
            {{ csrf_field() }}
            <p>Seleziona Persona</p>
            <livewire:search-popolazione
                name_input="persona_id"
                :multiple="false"
                placeholder="--- Inserisci nominativo ---"
            />
        </form>
    </template>
    <template slot="modal-button">
        <button
            class="btn btn-success"
            form="assegnaEsSpirituali{{ $esercizio->id }}"
        >
            Salva
        </button>
    </template>
</my-modal>
