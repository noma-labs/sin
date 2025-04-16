@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Persona"])

    <div class="row">
        <div class="col-md-4 offset-md-4">
            <h4>Dati Anagrafici</h4>
            <form
                method="POST"
                action="{{ route("nomadelfia.person.identity.store") }}"
            >
                @csrf
                <div class="mb-3">
                    <label for="fornominativo" class="form-label">
                        Nominativo:
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="fornominativo"
                        name="nominativo"
                        value="{{ old("nominativo") }}"
                        placeholder="Nominativo"
                    />
                </div>
                <div class="mb-3">
                    <label for="fornome" class="form-label">Nome:</label>
                    <input
                        type="text"
                        class="form-control"
                        id="fornome"
                        name="nome"
                        value="{{ old("nome") }}"
                        placeholder="Nome"
                    />
                </div>
                <div class="mb-3">
                    <label for="forcognome" class="form-label">Cognome:</label>
                    <input
                        type="text"
                        class="form-control"
                        id="forcognome"
                        name="cognome"
                        placeholder="Cognome"
                        value="{{ old("cognome") }}"
                    />
                </div>
                <div class="mb-3">
                    <label for="fornascita" class="form-label">
                        Data di Nascita:
                    </label>
                    <input
                        type="date"
                        class="form-control"
                        name="data_nascita"
                        value="{{ old("data_nascita") }}"
                    />
                </div>
                <div class="mb-3">
                    <label for="forluogo" class="form-label">
                        Luogo di nascita:
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="forluogo"
                        placeholder="Luogo di nascita"
                        name="luogo_nascita"
                        value="{{ old("luogo_nascita") }}"
                    />
                </div>
                <div class="mb-3">
                    <label for="forluogo" class="form-label">Sesso:</label>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="sesso"
                            value="M"
                            id="male"
                            @if(old('sesso')=='M') checked @endif
                        />
                        <label class="form-check-label" for="female">
                            Maschio
                        </label>
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="sesso"
                            value="F"
                            id="female"
                            @if(old('sesso')=='F') checked @endif
                        />
                        <label class="form-check-label" for="male">
                            Femmina
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <button class="btn btn-success" value="true" type="submit">
                        Inserisci
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
