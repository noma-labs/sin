@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi elaborato"])
    <form method="POST" action="{{ route("scuola.elaborati.insert") }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <label for="xTitolo" class="control-label">
                            Titolo (*)
                        </label>
                        <input
                            class="form-control"
                            type="text"
                            name="dimensione"
                            value="{{ old("titolo") }}"
                        />
                    </div>
                    <div class="col-md-4">
                        <label for="descrizione">Descrizione</label>
                        <input
                            class="form-control"
                            type="text"
                            name="descrizione"
                            value="{{ old("descrizione") }}"
                        />
                    </div>
                    <div class="col-md-4">
                        <label for="critica">Collocazione</label>
                        <input
                            class="form-control"
                            type="text"
                            name="collocazione"
                            value="{{ old("collocazione") }}"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="isbn">Studente/Classe</label>
                    </div>
                    <div class="col-md-4">
                        <label for="data_pubblicazione">Data Creazione</label>
                        <input
                            type="text"
                            class="form-control"
                            id="dataPubblicazione"
                            value="{{ old("data_creazione") }}"
                            name="data_creazione"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="xNote" class="control-label">Note</label>
                        <textarea
                            class="form-control"
                            name="xNote"
                            class="text"
                            rows="2"
                        >
{{ old("xNote") }}</textarea
                        >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <p class="text-right text-danger">
                            Le informazioni segnate con (*) sono obbligatorie.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button
                            class="btn btn-success"
                            name="_addonly"
                            value="true"
                            type="submit"
                        >
                            Salva
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6" v-pre></div>
        </div>
    </form>
@endsection
