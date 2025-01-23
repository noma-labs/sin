<x-modal
    modal-title="Modifica CQC"
    button-title="Modifica"
    button-style="btn-warning my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formEditCQC"
            action="{{ route("patente.cqc.modifica", $patente->numero_patente) }}"
        >
            @csrf
            @method("PUT")
            <div class="row">
                <div class="col-8">
                    @foreach ($cqcs as $cqc)
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                name="cqc[{{ $cqc->id }}][id]"
                                type="checkbox"
                                id="{{ $cqc->id }}"
                                value="{{ $cqc->id }}"
                                @if ($patente->cqc->contains($cqc->id))
                                    checked
                                @endif
                            />
                            <label
                                class="form-check-label"
                                for="{{ $cqc->id }}"
                            >
                                {{ $cqc->categoria }}
                            </label>
                            <div class="col-md-4">
                                <label>Rilasciata il:</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    name="cqc[{{ $cqc->id }}][data_rilascio]"
                                    @if ($patente->cqc->contains($cqc->id))
                                        value="{{ $patente->cqc->find($cqc->id)->pivot->data_rilascio }}"
                                    @endif
                                />
                            </div>
                            <div class="col-md-4">
                                <label>Valida fino al:</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    name="cqc[{{ $cqc->id }}][data_scadenza]"
                                    @if ($patente->cqc->contains($cqc->id))
                                        value="{{ $patente->cqc->find($cqc->id)->pivot->data_scadenza }}"
                                    @endif
                                />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-primary" form="formEditCQC">Salva</button>
    </x-slot>
</x-modal>
