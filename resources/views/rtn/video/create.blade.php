@extends("rtn.index")

@section("title", "Inserisci Video")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Video Archivio Professionale"])
    <form method="post" action="{{ route("rtn.video.store") }}">
        {{ csrf_field() }}

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-group row">
                    <label
                        for="forDataRegistrazione"
                        class="col-md-4 col-form-label"
                    >
                        Data Registrazione
                    </label>
                    <div class="col-md-8">
                        <input
                            type="date"
                            class="form-control"
                            id="forDataRegistrazione"
                            placeholder="--inserisci data"
                        />
                    </div>
                </div>
                <div class="form-group row">
                    <label
                        for="forDataTrasmission"
                        class="col-md-4 col-form-label"
                    >
                        Data Ultima trasmissione
                    </label>
                    <div class="col-md-8">
                        <input
                            type="date"
                            class="form-control"
                            id="forDataTrasmission"
                            placeholder="--inserisci categoria--"
                        />
                    </div>
                </div>
                <div class="form-group row">
                    <label
                        for="forDataTrasmission"
                        class="col-md-4 col-form-label"
                    >
                        Categoria Evento
                    </label>
                    <div class="col-md-8">
                        <input
                            type="password"
                            class="form-control"
                            id="forCategoriaEvento"
                        />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputOperatore" class="col-md-4 col-form-label">
                        Operatore/i
                    </label>
                    <div class="col-md-8">
                        <input
                            type="text"
                            class="form-control"
                            id="inputOperatore"
                            placeholder="--inserisci operatori--"
                        />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputOperatore" class="col-md-4 col-form-label">
                        Località
                    </label>
                    <div class="col-md-8">
                        <input
                            type="number"
                            class="form-control"
                            id="inputFineMinuti"
                            placeholder="--inserisci Località"
                        />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputOperatore" class="col-md-4 col-form-label">
                        Argomento
                    </label>
                    <div class="col-md-8">
                        <input
                            type="date"
                            class="form-control"
                            id="forDataRegistrazione"
                            placeholder="--inserisci Argomento"
                        />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPersona" class="col-md-4 col-form-label">
                        Persone
                    </label>
                    <div class="col-md-8">
                        <livewire:search-persona />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">
                            Inserisci
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
