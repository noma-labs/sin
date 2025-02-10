@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Gestione libro"])

    <div class="row mb-3">
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
                        @csrf
                        @method("PUT")
                        <button form="restoreBook" class="btn btn-info my-2">
                            Ripristina Libro
                        </button>
                    </form>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Collocazione</label>
                    <input
                        type="text"
                        class="form-control"
                        value="{{ $libro->collocazione }}"
                        disabled
                    />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="titolo">Titolo</label>
                    <input
                        type="text"
                        class="form-control"
                        id="titolo"
                        value="{{ $libro->titolo }}"
                        disabled
                    />
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="autore">Autori</label>
                    <livewire:search-autore
                        :persone_id="$libro->autori()->pluck('id')->toArray()"
                        name_input="xIdAutore"
                        :multiple="true"
                    />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="autore">Editori</label>
                    <livewire:search-editore
                        :persone_id="$libro->editori()->pluck('id')->toArray()"
                        name_input="xIdEditore"
                        :tmultiple="true"
                    />
                </div>
                <div class="col-md-12">
                    <label class="form-label" for="autore">
                        Classificazione
                    </label>
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
                <div class="col-md-4">
                    <label class="form-label" for="isbn">ISBN</label>
                    <input
                        class="form-control"
                        type="text"
                        value="{{ $libro->isbn }}"
                        disabled
                    />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data pubblicazione</label>
                    <input
                        class="form-control"
                        type="date"
                        value="{{ $libro->data_pubblicazione }}"
                        disabled
                    />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Categoria</label>
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
                    <label class="form-label">Dimensione</label>
                    <input
                        class="form-control"
                        type="text"
                        value="{{ $libro->dimensione }}"
                        disabled
                    />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Critica</label>
                    <input
                        class="form-control"
                        type="text"
                        value="{{ $libro->critica }}"
                        disabled
                    />
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label" for="autore">Note</label>
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
                action="{{ route("books.labels.store-book", ["idLibro" => $libro->id]) }}"
                method="post"
            >
                @csrf
            </form>

            <form
                id="removeLibroPrint"
                action="{{ route("books.labels.delete-book", ["idLibro" => $libro->id]) }}"
                method="post"
            >
                @csrf
            </form>

            <div class="row">
                <div class="d-grid gap-2 d-md-block">
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

                    @if (! $libro->trashed())
                        <a
                            class="btn btn-warning"
                            href="{{ route("books.labels.print", ["idLibro" => $libro->id]) }}"
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
