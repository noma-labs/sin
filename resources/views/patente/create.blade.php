@extends("patente.index")

@section("content")
    @include("partials.header", ["title" => "Inserisci nuova patente"])

    <form method="POST" action="{{ route("patente.store") }}">
        @csrf
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="row">
                    <div class="col-md-12">
                        <label for="numero_patente">Persona:</label>
                        <livewire:search-popolazione name_input="persona_id" />
                    </div>
                </div>
                <div class="row">
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
                <div class="row">
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
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="note">Note:</label>
                        <textarea class="form-control" name="note">
{{ old("note") }}</textarea
                        >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
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
