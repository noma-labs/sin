@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Modifica Dati anagrafici"])

    <div class="row justify-content-center">
        <div class="col-4">
            <form
                class="form"
                method="POST"
                action="{{ route("nomadelfia.persone.anagrafica.update", ["idPersona" => $persona->id]) }}"
            >
                @method("PUT")
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Nome</label>
                    <input
                        id="name"
                        type="text"
                        class="form-control"
                        name="nome"
                        value="{{ old("nome") ? old("nome") : $persona->nome }}"
                    />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="surname">Cognome</label>
                    <input
                        id="surname"
                        type="text"
                        class="form-control"
                        name="cognome"
                        value="{{ old("cognome") ? old("cognome") : $persona->cognome }}"
                    />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="fiscalcode">Codice Fiscale</label>
                    <input
                        id="fiscalcode"
                        type="text"
                        class="form-control"
                        name="codicefiscale"
                        value="{{ old("codicefiscale") ? old("codicefiscale") : $persona->cf }}"
                    />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="birthdate">Data Nascita</label>
                    <input
                        id="birthdate"
                        class="form-control"
                        type="date"
                        name="datanascita"
                        value="{{ old("datanascita") ? old("datanascita") : $persona->data_nascita }}"
                    />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="birthplace">Luogo Nascita</label>
                    <input
                        id="birthplace"
                        type="text"
                        class="form-control"
                        name="luogonascita"
                        value="{{ old("luogonascita") ? old("luogonascita") : $persona->provincia_nascita }}"
                    />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="decesso">Data Decesso</label>
                    <input
                        id="birthdate"
                        class="form-control"
                        type="date"
                        name="data_decesso"
                        value="{{ old("data_decesso") ? old("data_decesso") : $persona->data_decesso }}"
                    />
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input
                            id="sexmen"
                            class="form-check-input"
                            type="radio"
                            name="sesso"
                            value="M"
                            {{ $persona->sesso == "M" ? "checked" : "" }}
                        />
                        <label class="form-label" for="sexmen" class="form-check-label">
                            Maschio
                        </label>
                    </div>
                    <div class="form-check">
                        <input
                            id="sexwomen"
                            class="form-check-input"
                            type="radio"
                            name="sesso"
                            value="F"
                            {{ $persona->sesso == "F" ? "checked" : "" }}
                        />
                        <label class="form-label" for="sexwomen" class="form-check-label">
                            Femmina
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="biography">Biografia</label>
                    <textarea
                        id="biography"
                        class="form-control"
                        name="biografia"
                        rows="10"
                    >
                    {{ $persona->biografia }}
                </textarea>
                </div>

                <div class="mb-3">
                    <button class="btn btn-danger">Torna indietro</button>
                    <button class="btn btn-success" type="submit">
                        Salva Modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
