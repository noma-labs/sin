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
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name">Nome</label>
                    <input
                        id="name"
                        type="text"
                        class="form-control"
                        name="nome"
                        value="{{ old("nome") ? old("nome") : $persona->nome }}"
                    />
                </div>
                <div class="form-group">
                    <label for="surname">Cognome</label>
                    <input
                        id="surname"
                        type="text"
                        class="form-control"
                        name="cognome"
                        value="{{ old("cognome") ? old("cognome") : $persona->cognome }}"
                    />
                </div>
                <div class="form-group">
                    <label for="fiscalcode">Codice Fiscale</label>
                    <input
                        id="fiscalcode"
                        type="text"
                        class="form-control"
                        name="codicefiscale"
                        value="{{ old("codicefiscale") ? old("codicefiscale") : $persona->cf }}"
                    />
                </div>
                <div class="form-group">
                    <label for="birthdate">Data Nascita</label>
                    <date-picker
                        id="birthdate"
                        :bootstrap-styling="true"
                        value="{{ old("datanascita") ? old("datanascita") : $persona->data_nascita }}"
                        format="yyyy-MM-dd"
                        name="datanascita"
                    ></date-picker>
                </div>
                <div class="form-group">
                    <label for="birthplace">Luogo Nascita</label>
                    <input
                        id="birthplace"
                        type="text"
                        class="form-control"
                        name="luogonascita"
                        value="{{ old("luogonascita") ? old("luogonascita") : $persona->provincia_nascita }}"
                    />
                </div>
                <div class="form-group">
                    <label for="decesso">Data Decesso</label>
                    <date-picker
                        id="decesso"
                        :bootstrap-styling="true"
                        value="{{ old("data_decesso") ? old("data_decesso") : $persona->data_decesso }}"
                        format="yyyy-MM-dd"
                        name="data_decesso"
                    ></date-picker>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input
                            id="sexmen"
                            class="form-check-input"
                            type="radio"
                            name="sesso"
                            value="M"
                            {{ $persona->sesso == "M" ? "checked" : "" }}
                        />
                        <label for="sexmen" class="form-check-label">
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
                        <label for="sexwomen" class="form-check-label">
                            Femmina
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="biography">Biografia</label>
                    <textarea
                        id="biography"
                        class="form-control"
                        name="biografia"
                        rows="10"
                    >
{{ $persona->biografia }}</textarea
                    >
                </div>

                <div class="form-group">
                    <button class="btn btn-danger">Torna indietro</button>
                    <button class="btn btn-success" type="submit">
                        Salva Modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
