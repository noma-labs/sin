<x-modal
    modal-title="Assegna Famiglia alla persona"
    button-title="Assegna Famiglia"
    button-style="btn-secondary my-2"
>
    <x-slot:body>
        <form
            method="POST"
            id="formAddPersonToFamily"
            action="{{ route("nomadelfia.person.add-family", $persona->id) }}"
        >
            @csrf
            <livewire:search-famiglia
                name_input="family_id"
                placeholder="---Cerca famiglia---"
            />
            <select class="form-select mt-3" name="type">
                <option selected>--- scegli posizione---</option>
                @foreach (App\Nomadelfia\Famiglia\Models\Famiglia::getEnum("Posizione") as $posizione)
                    <option value="{{ $posizione }}">
                        {{ $posizione }}
                    </option>
                @endforeach
            </select>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-success" form="formAddPersonToFamily">
            Salva
        </button>
    </x-slot>
</x-modal>
