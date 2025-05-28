@extends("agraria.index")

@section("title", "Modifica Manutenzione")

@section("content")
    <div class="container mt-4">
        <div class="card card-mod">
            <div class="card-header">
                <h3 class="card-title mb-0">Modifica Manutenzione</h3>
            </div>
            <form
                method="POST"
                action="{{ route("agraria.maintenanace.update", $manutenzione->id) }}"
            >
                @csrf
                @method("PUT")
                <div class="card-body">
                    <div class="mb-3">
                        <label for="mezzo" class="form-label">
                            Mezzo Agricolo
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $manutenzione->mezzo->nome }}"
                            disabled
                        />
                        <input
                            type="hidden"
                            name="mezzo"
                            value="{{ $manutenzione->mezzo->id }}"
                        />
                    </div>
                    <div class="mb-3">
                        <label for="data" class="form-label">Data</label>
                        <input
                            type="date"
                            name="data"
                            id="data"
                            class="form-control"
                            value="{{ $manutenzione->data }}"
                            required
                        />
                    </div>
                    <div class="mb-3">
                        <label for="ore" class="form-label">Ore</label>
                        <input
                            type="number"
                            name="ore"
                            id="ore"
                            class="form-control"
                            value="{{ $manutenzione->ore }}"
                            required
                        />
                    </div>
                    <div class="mb-3">
                        <label for="spesa" class="form-label">Spesa</label>
                        <input
                            type="number"
                            step="0.01"
                            name="spesa"
                            id="spesa"
                            class="form-control"
                            value="{{ $manutenzione->spesa }}"
                        />
                    </div>
                    <div class="mb-3">
                        <label for="persona" class="form-label">Persona</label>
                        <input
                            type="text"
                            name="persona"
                            id="persona"
                            class="form-control"
                            value="{{ $manutenzione->persona }}"
                            required
                        />
                    </div>
                    <div class="mb-3">
                        <label for="straordinarie" class="form-label">
                            Lavori Extra (Straordinarie)
                        </label>
                        <input
                            type="text"
                            name="straordinarie"
                            id="straordinarie"
                            class="form-control"
                            value="{{ $manutenzione->lavori_extra }}"
                        />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Manutenzioni Programmate
                        </label>
                        <div class="form-check">
                            @foreach ($programmate as $prog)
                                <div>
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="programmate[]"
                                        id="programmata_{{ $prog->id }}"
                                        value="{{ $prog->id }}"
                                        @if($manutenzione->programmate->contains($prog->id)) checked @endif
                                    />
                                    <label
                                        class="form-check-label"
                                        for="programmata_{{ $prog->id }}"
                                    >
                                        {{ $prog->nome }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a
                        href="{{ route("agraria.maintenanace.show", $manutenzione->id) }}"
                        class="btn btn-secondary"
                    >
                        Annulla
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Salva Modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
