@extends("rtn.index")

@section("title", "Inserisci Video")

@section("archivio")
    @include("partials.header", ["title" => "Aggiungi Video Archivio Professionale"])
    <form method="post" action="{{ route("rtn.video.store") }}">
        {{csrf_field()}}
        <div class="form-group">
            <label for="inputPersona" class="col-sm-2 col-form-label">
                Persona
            </label>
            <div class="col-sm-10">
                <livewire:search-persona
                    placeholder="Cerca persona"
                    noResultsMessage="Nessun risultato"
                />
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputDispositivo" class="col-sm-2 col-form-label">
                    Dispositivo
                </label>
                <div class="col-sm-10">
                    <input
                        type="text"
                        class="form-control"
                        id="inputDispositivo"
                        placeholder="--inserisci dispositivo--"
                    />
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="inputCategoria" class="col-sm-2 col-form-label">
                    Categoria
                </label>
                <div class="col-sm-10">
                    <input
                        type="text"
                        class="form-control"
                        id="inputCategoria"
                        placeholder="--inserisci categoria--"
                    />
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label
                    for="inputCategoriaEvento"
                    class="col-sm-2 col-form-label"
                >
                    CategoriaEvento
                </label>
                <div class="col-sm-10">
                    <input
                        type="text"
                        class="form-control"
                        id="inputCategoriaEvento"
                        placeholder="--inserisci categoriaevento"
                    />
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="inputRecord" class="col-sm-2 col-form-label">
                    Record
                </label>
                <div class="col-sm-10">
                    <input
                        type="text"
                        class="form-control"
                        id="inputRecord"
                        placeholder="--inserisci categoria--"
                    />
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputInizioMinuti" class="col-sm-2 col-form-label">
                    Inizio Minuti
                </label>
                <div class="col-sm-10">
                    <input
                        type="number"
                        class="form-control"
                        id="inputInizioMinuti"
                        placeholder="--inserisci categoriaevento"
                    />
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="inputFineMinuti" class="col-sm-2 col-form-label">
                    Fine Minuti
                </label>
                <div class="col-sm-10">
                    <input
                        type="number"
                        class="form-control"
                        id="inputFineMinuti"
                        placeholder="--inserisci categoria--"
                    />
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputInizioMinuti" class="col-sm-2 col-form-label">
                    Data Registrazione
                </label>
                <div class="col-sm-10">
                    <input
                        type="date"
                        class="form-control"
                        id="inputInizioMinuti"
                        placeholder="--inserisci categoriaevento"
                    />
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="inputFineMinuti" class="col-sm-2 col-form-label">
                    Data Ultima trasmissione
                </label>
                <div class="col-sm-10">
                    <input
                        type="date"
                        class="form-control"
                        id="inputFineMinuti"
                        placeholder="--inserisci categoria--"
                    />
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="inputPersona" class="col-sm-2 col-form-label">
                Località
            </label>
            <div class="col-sm-10">
                <input
                    type="text"
                    class="form-control"
                    id="inputFineMinuti"
                    placeholder="--inserisci categoria--"
                />
            </div>
        </div>

        <div class="form-group">
            <label for="inputPersona" class="col-sm-2 col-form-label">
                Nome Cartella
            </label>
            <div class="col-sm-10">
                <input
                    type="text"
                    class="form-control"
                    id="inputFineMinuti"
                    placeholder="--inserisci categoria--"
                />
            </div>
        </div>

        <div class="form-group">
            <label for="inputPersona" class="col-sm-2 col-form-label">
                Argomento
            </label>
            <div class="col-sm-10">
                <input
                    type="text"
                    class="form-control"
                    id="inputFineMinuti"
                    placeholder="--inserisci categoria--"
                />
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">Inserisci</button>
            </div>
        </div>
    </form>
@endsection
