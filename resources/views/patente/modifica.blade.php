@extends("patente.index")

@section("archivio")
    <sin-header title="Modifica Patente"></sin-header>
    <patente-modfica
        api-patente="{{ route("api.patente", ["numero" => $patente->numero_patente]) }}"
        api-patente-modifica="{{ route("api.patente.modifica", ["numero" => $patente->numero_patente]) }}"
        web-patente-elimina="{{ route("patente.elimina", $patente->numero_patente) }}"
        api-patente-persone="{{ route("api.patente.persone.senzapatente") }}"
        api-patente-rilascio="{{ route("api.patente.rilascio") }}"
        api-patente-categorie="{{ route("api.patente.categorie") }}"
        api-patente-cqc="{{ route("api.patente.cqc") }}"
    >
        <template slot="persona-info">
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
        </template>
    </patente-modfica>
@endsection
