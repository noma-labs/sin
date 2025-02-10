<x-modal
    modal-title="Assegna Es. Spirituali"
    button-title="Aggiungi Persona"
    button-style="btn-success my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="assegnaEsSpirituali{{ $esercizio->id }}"
            action="{{ route("nomadelfia.esercizi.assegna", ["id" => $esercizio->id]) }}"
        >
            @csrf
            <p>Seleziona Persona</p>
            <livewire:search-popolazione name_input="persona_id" />
        </form>
    </x-slot>
    <x-slot:footer>
        <button
            class="btn btn-success"
            form="assegnaEsSpirituali{{ $esercizio->id }}"
        >
            Salva
        </button>
    </x-slot>
</x-modal>
