@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Modifica Prestito"])

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form
                id="form-modifica"
                class="form"
                method="POST"
                action="{{ route("books.loans.update", $prestito->id) }}"
            >
                @csrf
                @method("PUT")
                <div class="row">
                    <div class="col-md-6">
                        <div class="">
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
                        <div class="">
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
                        <div class="">
                            <label>Cliente</label>
                            <livewire:search-persona
                                :persone_id="$prestito->cliente->id"
                                name_input="persona_id"
                            />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="">
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
                        <div class="">
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
                            href="{{ route("books.loans") }}"
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
