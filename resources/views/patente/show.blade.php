@extends("patente.index")

@section("content")
    @include("partials.header", ["title" => "Modifica Patente"])

    <div class="row">
        <div class="col-md-6">
            <form
                action="{{ route("patente.update", ["numero" => $patente->numero_patente]) }}"
                method="POST"
                id="edit-patente"
            >
                @csrf
                @method("PUT")
                <div class="row">
                    <div class="col-md-6">
                        <label for="numero_patente">Persona:</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $patente->persona->nominativo }}"
                            disabled
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="nome_cognome">Nome Cognome:</label>

                        @if ($patente->persona->nome and $patente->persona->cognome)
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $patente->persona->nome }} {{ $patente->persona->cognome }}"
                                disabled
                            />
                        @else
                            <input
                                type="text"
                                class="form-control"
                                value="---dato non disponibile---"
                                disabled
                            />
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="data_nascita">Data di nascita:</label>

                        @if ($patente->persona->data_nascita)
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $patente->persona->data_nascita }}"
                                disabled
                            />
                        @else
                            <input
                                type="text"
                                class="form-control"
                                value="---dato non disponibile---"
                                disabled
                            />
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label for="luogo_nascita">Luogo di nascita:</label>

                        @if ($patente->persona->provincia_nascita)
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $patente->persona->provincia_nascita }}"
                                disabled
                            />
                        @else
                            <input
                                type="text"
                                class="form-control"
                                value="---dato non disponibile---"
                                disabled
                            />
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="data_rilascio_patente">
                            Patente rilasciata il:
                        </label>
                        <input
                            type="date"
                            class="form-control"
                            name="data_rilascio_patente"
                            value="{{ $patente->data_rilascio_patente }}"
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="rilasciata_dal">Rilasciata da:</label>
                        <input
                            type="text"
                            class="form-control"
                            name="rilasciata_dal"
                            value="{{ $patente->rilasciata_dal }}"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="data_scadenza_patente">
                            Patente valida fino al:
                        </label>
                        <input
                            type="date"
                            class="form-control"
                            name="data_scadenza_patente"
                            value="{{ $patente->data_scadenza_patente }}"
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="numero_patente">Numero Patente:</label>
                        <input
                            type="text"
                            class="form-control"
                            name="numero_patente"
                            value="{{ $patente->numero_patente }}"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="note">Note:</label>
                        <textarea class="form-control" name="note">
             {{ $patente->note }} </textarea
                        >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="assegnaCommissione"
                                id="ycommissione"
                                {{ $patente->hasCommissione() ? "checked" : "" }}
                            />
                            <label class="form-check-label" for="ycommissione">
                                Con commissione.
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-md-3">
                        <button
                            type="submit"
                            form="edit-patente"
                            class="btn btn-primary"
                        >
                            Salva
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-5">
                    <div class="card">
                        <h5 class="card-header">Categorie</h5>
                        <div class="card-body">
                            <ul class="list-inline">
                                @foreach ($patente->categorie as $cat)
                                    <li class="list-inline-item">
                                        <span class="badge bg-primary">
                                            {{ $cat->categoria }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            @include("patente.templates.editCategorie", ["categorie" => $categorie])
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <h5 class="card-header">C.Q.C</h5>
                        <div class="card-body">
                            @foreach ($patente->cqc as $c)
                                <div>
                                    <b>{{ $c->categoria }}</b>
                                    <span class="badge bg-success">
                                        {{ $c->pivot->data_rilascio }}
                                    </span>
                                    <span class="badge bg-danger">
                                        {{ $c->pivot->data_scadenza }}
                                    </span>
                                </div>
                            @endforeach

                            @include("patente.templates.editCQC", ["categorie" => $categorie])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
