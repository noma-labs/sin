@extends("biblioteca.libri.index")

@section("content")
    @include("partials.header", ["title" => "Gestione libro"])

    <div class="row">
        <div class="col-md-8">
            @if ($libro->trashed())
                <div class="p-3 mb-2 bg-danger text-white">
                    <h2>Libro Eliminato.</h2>
                    Motivazione: {{ $libro->deleted_note }}
                    <form
                        id="restoreBook"
                        action="{{ route("books.restore", $libro->id) }}"
                        method="post"
                    >
                        {{ csrf_field() }}
                        @method("PUT")
                        <button form="restoreBook" class="btn btn-info my-2">
                            Ripristina Libro
                        </button>
                    </form>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <label>Collocazione</label>
                    <input
                        type="text"
                        class="form-control"
                        value="{{ $libro->collocazione }}"
                        disabled
                    />
                </div>
                <div class="col-md-6">
                    <label for="titolo">Titolo</label>
                    <input
                        type="text"
                        class="form-control"
                        id="titolo"
                        value="{{ $libro->titolo }}"
                        disabled
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="autore">Autori</label>
                    <livewire:search-autore
                        :persone_id="$libro->autori()->pluck('id')->toArray()"
                        name_input="xIdAutore"
                        :multiple="true"
                    />
                </div>
                <div class="col-md-6">
                    <label for="autore">Editori</label>
                    <livewire:search-editore
                        :persone_id="$libro->editori()->pluck('id')->toArray()"
                        name_input="xIdEditore"
                        :tmultiple="true"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="autore">Classificazione</label>
                    @if ($libro->classificazione)
                        <input
                            type="text"
                            class="form-control"
                            id="autore"
                            value="{{ $libro->classificazione->descrizione }}"
                            disabled
                        />
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="isbn">ISBN</label>
                    <input
                        class="form-control"
                        type="text"
                        value="{{ $libro->isbn }}"
                        disabled
                    />
                </div>
                <div class="col-md-4">
                    <label>Data pubblicazione</label>
                    <input
                        class="form-control"
                        type="date"
                        value="{{ $libro->data_pubblicazione }}"
                        disabled
                    />
                </div>
                <div class="col-md-4">
                    <label>Categoria</label>
                    <input
                        class="form-control"
                        type="text"
                        value="{{ $libro->categoria }}"
                        disabled
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label>Dimensione</label>
                    <input
                        class="form-control"
                        type="text"
                        value="{{ $libro->dimensione }}"
                        disabled
                    />
                </div>
                <div class="col-md-6">
                    <label>Critica</label>
                    <input
                        class="form-control"
                        type="text"
                        value="{{ $libro->critica }}"
                        disabled
                    />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <label for="autore">Note</label>
                    <input
                        type="text"
                        class="form-control"
                        id="autore"
                        value="{{ $libro->note }}"
                        disabled
                    />
                </div>
            </div>
            <form
                id="addLibroPrint"
                action="{{ route("libri.etichette.aggiungi.libro", ["idLibro" => $libro->id]) }}"
                method="post"
            >
                {{ csrf_field() }}
            </form>

            <form
                id="removeLibroPrint"
                action="{{ route("libri.etichette.rimuovi.libro", ["idLibro" => $libro->id]) }}"
                method="post"
            >
                {{ csrf_field() }}
            </form>

            <div class="row my-2">
                <div class="col-md-6">
                    @if (! $libro->trashed())
                        <a
                            class="btn btn-success"
                            href="{{ route("books.edit", ["id" => $libro->id]) }}"
                        >
                            Modifica
                        </a>
                        @if ($libro->tobe_printed == 0)
                            <button
                                class="btn btn-success"
                                form="addLibroPrint"
                                type="submit"
                            >
                                Aggiungi stampa etichetta
                            </button>
                        @else
                            <button
                                class="btn btn-warning"
                                form="removeLibroPrint"
                                type="submit"
                            >
                                Rimuovi stampa etichetta
                            </button>
                        @endif
                    @endif
                </div>
                <div class="col-md-6">
                    @if (! $libro->trashed())
                        <a
                            class="btn btn-warning"
                            href="{{ route("libri.etichette.stampa", ["idLibro" => $libro->id]) }}"
                        >
                            Genera Etichetta
                        </a>
                    @endif

                    <a
                        class="btn btn-info"
                        href="#"
                        onclick="window.history.back(); return false;"
                    >
                        Torna indietro
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info border-info">
                <div class="card-header">Prestiti attivo</div>
                <div class="card-body">
                    @forelse ($prestitiAttivi as $prestito)
                        <p>
                            Cliente:
                            <strong>
                                {{ $prestito->cliente->nominativo }}
                            </strong>
                        </p>
                        <p>
                            Data Inizio Prestito:
                            <strong>
                                {{ $prestito->data_inizio_prestito }}
                            </strong>
                        </p>
                        <p>
                            Data Fine Prestito:
                            <strong>
                                {{ $prestito->data_fine_prestito }}
                            </strong>
                        </p>
                        <p>
                            Bibliotecario:
                            <strong>
                                {{ $prestito->bibliotecario->nominativo }}
                            </strong>
                        </p>
                    @empty
                        <p class="bg-danger">Nessuna prenotazione attiva</p>
                    @endforelse

                    @if ($libro->inPrestito())
                        <a
                            class="btn btn-primary"
                            href="{{ route("books.loans.show", $prestito->id) }}"
                        >
                            Gestisci prestito
                        </a>
                    @else
                        <a
                            class="btn btn-primary"
                            href="{{ route("books.borrow", $libro->id) }}"
                        >
                            Dai in Prestito
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
