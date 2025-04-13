<x-modal
    modal-title="Aggiungi componente alla famiglia"
    button-title="Aggiungi Componente"
    button-style="btn-primary my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formComponente"
            action="{{ route("nomadelfia.famiglie.componente.assegna", ["id" => $famiglia->id]) }}"
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
                    Posizione Famiglia
                </label>
                <div class="col-8">
                    <select class="form-select" name="posizione">
                        <option value="" selected>
                            ---scegli posizione---
                        </option>
                        @foreach (App\Nomadelfia\Famiglia\Models\Famiglia::getEnum("Posizione") as $posizione)
                            <option value="{{ $posizione }}">
                                {{ $posizione }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <label for="example-text-input" class="col-4 form-label">
                    Stato:
                </label>
                <div class="col-8">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="stato"
                            id="stato1"
                            value="1"
                            checked
                        />
                        <label class="form-check-label" for="stato1">
                            Includi nel nucleo familiare
                        </label>
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="stato"
                            id="stato2"
                            value="0"
                        />
                        <label class="form-check-label" for="stato2">
                            Non includere nel nucleo familiare
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <label for="example-text-input" class="col-4 form-label">
                    Note:
                </label>
                <div class="col-8">
                    <!-- <input type="date" class="form-control" name="note" placeholder="Data entrata nella famiglia" > -->
                    <textarea
                        class="form-control"
                        name="note"
                        id="exampleFormControlTextarea1"
                        rows="3"
                    ></textarea>
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-danger" form="formComponente">Salva</button>
    </x-slot>
</x-modal>
