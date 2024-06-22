@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi elaborato"])
    <form
        method="POST"
        action="{{ route("scuola.elaborati.store") }}"
        enctype="multipart/form-data"
    >
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <label for="titolo" class="control-label">Titolo</label>
                        <input
                            class="form-control"
                            type="text"
                            name="titolo"
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
                        <label for="xfile" class="form-label">
                            Scegli file
                        </label>
                        <input
                            class="form-control"
                            type="file"
                            id="xfile"
                            name="file"
                        />
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
                            type="submit"
                            value="Upload"
                        >
                            Salva
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
