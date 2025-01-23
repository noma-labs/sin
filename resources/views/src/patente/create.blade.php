@extends("patente.index")

@section("content")
    @include("partials.header", ["title" => "Inserisci nuova patente"])

    <form method="POST" action="{{ route("patente.store") }}">
        @csrf
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="numero_patente">Persona:</label>
                        <livewire:search-popolazione name_input="persona_id" />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="data_rilascio_patente">
                            Patente rilasciata il:
                        </label>
                        <input
                            class="form-control"
                            type="date"
                            name="data_rilascio_patente"
                            value="{{ old("data_rilascio_patente") }}"
                        />
                    </div>

                    <div class="col-md-6">
                        <label for="rilasciata_dal">Rilasciata dal:</label>
                        <input
                            class="form-control"
                            type="text"
                            name="rilasciata_dal"
                            value="{{ old("rilasciata_dal") }}"
                        />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="data_scadenza_patente">
                            Patente valida fino al:
                        </label>
                        <input
                            class="form-control"
                            type="date"
                            name="data_scadenza_patente"
                            value="{{ old("data_scadenza_patente") }}"
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="numero_patente">Numero Patente:</label>
                        <input
                            type="text"
                            class="form-control"
                            autocomplete="off"
                            name="numero_patente"
                            value="{{ old("numero_patente") }}"
                        />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="note">Note:</label>
                        <textarea class="form-control" name="note">
{{ old("note") }}</textarea
                        >
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="mb-3 col-md-12">
                        <label for="">Categorie:</label>
                        <div>
                            @foreach ($categorie as $cat)
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        name="categorie[]"
                                        type="checkbox"
                                        id="{{ $cat->id }}"
                                        value="{{ $cat->id }}"
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
                </div>
                <div class="row mb-3">
                    <div class="mb-3 col-md-12">
                        <div><label for="">C.Q.C:</label></div>
                        @foreach ($cqcs as $cqc)
                            <div class="form-check-inline">
                                <div class="col-md-4">
                                    <input
                                        class="form-check-input"
                                        name="cqc[{{ $cqc->id }}][id]"
                                        type="checkbox"
                                        id="{{ $cqc->id }}"
                                        value="{{ $cqc->id }}"
                                    />
                                    <label
                                        class="form-check-label"
                                        for="{{ $cqc->id }}"
                                    >
                                        {{ $cqc->categoria }}
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Rilasciata il:</label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        name="cqc[{{ $cqc->id }}][data_rilascio]"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Valida fino al:</label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        name="cqc[{{ $cqc->id }}][data_scadenza]"
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div><label for="">Commisione</label></div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="assegnaCommissione"
                                true-value="commissione"
                                false-value="null"
                                id="ycommissione"
                            />
                            <label class="form-check-label" for="ycommissione">
                                Con commissione.
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 offset-md-8">
                        <button type="submit" class="btn btn-primary">
                            Salva
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
