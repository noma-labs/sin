<x-modal
    modal-title="Modifica categorie"
    button-title="Modifica"
    button-style="btn-warning my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formEditCategorie"
            action="{{ route("patente.categorie.modifica", $patente->numero_patente) }}"
        >
            @csrf
            @method("PUT")
            <div class=" row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Categorie
                </label>
                <div class="col-8">
                    @foreach ($categorie as $cat)
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                name="categorie[]"
                                type="checkbox"
                                id="{{ $cat->id }}"
                                value="{{ $cat->id }}"
                                @if ($patente->categorie->contains($cat->id))
                                    checked
                                @endif
                            />
                            <label
                                class="form-check-label"
                                for="{{ $cat->id }}"
                            >
                                {{ $cat->categoria }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-primary" form="formEditCategorie">Salva</button>
    </x-slot>
</x-modal>
