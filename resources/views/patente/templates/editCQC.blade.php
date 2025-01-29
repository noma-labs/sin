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
            @foreach ($cqcs as $cqc)
                <div class="row">
                    <div class="col">
                        <input
                            class="form-check"
                            name="cqc[{{ $cqc->id }}][id]"
                            type="checkbox"
                            id="{{ $cqc->id }}"
                            value="{{ $cqc->id }}"
                            @if ($patente->cqc->contains($cqc->id))
                                checked
                            @endif
                        />
                        <label class="form-label" for="{{ $cqc->id }}">
                            {{ $cqc->categoria }}
                        </label>
                    </div>
                    <div class="col">
                        <label class="form-label">Rilasciata il:</label>
                        <input
                            type="date"
                            class="form-control"
                            name="cqc[{{ $cqc->id }}][data_rilascio]"
                            @if ($patente->cqc->contains($cqc->id))
                                value="{{ $patente->cqc->find($cqc->id)->pivot->data_rilascio }}"
                            @endif
                        />
                    </div>
                    <div class="col">
                        <label class="form-label">Valida fino al:</label>
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
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-primary" form="formEditCQC">Salva</button>
    </x-slot>
</x-modal>
