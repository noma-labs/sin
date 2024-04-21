@extends("biblioteca.libri.index")

@section("content")
    @include("partials.header", ["title" => "Modifica Prestito"])

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form
                id="form-modifica"
                class="form"
                method="POST"
                action="{{ route("libri.prestito.modifica", ["idPrestito" => $prestito->id]) }}"
            >
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="xDataPrenotazione">
                                Data Inizio Prestito
                            </label>
                            <input
                                type="date"
                                class="form-control"
                                value="{{ $prestito->data_inizio_prestito }}"
                                name="xDataPrenotazione"
                                id="datep"
                            />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label
                                for="xDataRestituzione"
                                class="control-label"
                            >
                                Data Fine Prestito
                            </label>
                            <input
                                type="date"
                                class="form-control"
                                name="xDataRestituzione"
                                id="dater"
                                placeholder="Data Restituzione"
                                value="{{ $prestito->data_fine_prestito }}"
                            />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Cliente</label>
                            <autocomplete
                                :selected="{{ $prestito->cliente()->pluck("nominativo", "id") }}"
                                placeholder="Inserisci nominativo..."
                                name="persona_id"
                                url="{{ route("api.biblioteca.clienti") }}"
                            ></autocomplete>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Note</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $prestito->note }}"
                                name="xNote"
                                placeholder="Inserisci le note"
                            />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="xIdBibliotecario">Bibliotecario</label>
                            <input
                                type="text"
                                class="form-control"
                                id="autore"
                                value="{{ $prestito->Bibliotecario->nominativo }}"
                                disabled
                            />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 my-2">
                        <button
                            class="btn btn-success m-1"
                            form="form-modifica"
                            type="submit"
                        >
                            Salva Modifiche
                        </button>
                        <a
                            class="btn btn-info m-1"
                            href="{{ route("libri.prestiti") }}"
                            role="button"
                        >
                            Torna Prestiti
                        </a>
                        <button class="btn btn-danger m-1 float-right">
                            Elimina prestito
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
