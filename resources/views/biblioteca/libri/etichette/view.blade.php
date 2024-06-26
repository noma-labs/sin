@extends("biblioteca.libri.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Etichette"])

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                Etichette da stampare:
                <strong>
                    {{ App\Biblioteca\Models\Libro::TobePrinted()->count() }}
                </strong>
                <a
                    href="#"
                    class="close"
                    data-dismiss="alert"
                    aria-label="close"
                >
                    &times;
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12 my-2">
                    <a
                        class="btn btn-info"
                        href="{{ route("libri.etichette.preview") }}"
                        role="button"
                    >
                        Anteprima
                    </a>
                    <a
                        class="btn btn-success"
                        href="{{ route("libri.etichette.stampa") }}"
                        role="button"
                    >
                        Esporta etichette
                    </a>
                    <form
                        class="float-right"
                        id="formRemoveAll"
                        action="{{ route("libri.etichette.rimuovi") }}"
                        method="post"
                    >
                        {{ csrf_field() }}
                        <button
                            class="btn btn-danger"
                            form="formRemoveAll"
                            type="submit"
                        >
                            Rimuovi tutte le etichette
                        </button>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if ($libriTobePrinted->count() > 0)
                        <table
                            class="table table-striped table-hover table-bordered"
                        >
                            <thead class="thead-inverse">
                                <tr>
                                    <th style="width: 10%">COLLOCAZIONE</th>
                                    <th style="width: 15%">TITOLO</th>
                                    <th style="width: 10%">OPERAZIONI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($libriTobePrinted as $libro)
                                    <tr>
                                        <td
                                            onclick="gotoLibroDetails({{ $libro->id }})"
                                        >
                                            {{ $libro->collocazione }}
                                        </td>
                                        <td
                                            onclick="gotoLibroDetails({{ $libro->id }})"
                                        >
                                            {{ $libro->titolo }}
                                        </td>
                                        <td>
                                            <form
                                                action="{{ route("libri.etichette.rimuovi.libro", ["idLibro" => $libro->id]) }}"
                                                method="post"
                                            >
                                                {{ csrf_field() }}
                                                <button
                                                    class="btn btn-danger"
                                                    type="submit"
                                                >
                                                    Rimuovi etichetta
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <div class="alert alert-danger">
                                        <strong>
                                            Nessuna ethichetta da stampare
                                        </strong>
                                    </div>
                                @endforelse
                            </tbody>
                        </table>
                    @endif

                    <!-- </div> -->
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info mb-3">
                <div class="card-header">Aggiungi etichette da stampare</div>
                <div class="card-body">
                    <h5 class="card-title">Ricerca per collocazione</h5>
                    <h6 class="card-subtitle mb-2 text-muted">
                        Inserisci l'intervallo delle collocazioni dei libri che
                        vuoi aggiungere o rimuovere dalla stampa delle
                        etichette.
                    </h6>
                    <form
                        method="POST"
                        class="form"
                        action="{{ route("libri.etichette.aggiungi") }}"
                    >
                        {{ csrf_field() }}
                        <h5>Dalla collocazione:</h5>
                        <search-collocazione
                            title="Collocazione ({{ App\Biblioteca\Models\ViewCollocazione::total() }}) - Lettere (*)"
                            name="fromCollocazione"
                            url-lettere="{{ route("api.biblioteca.collocazione") }}"
                            numeri-required="true"
                            numero-nuovo="false"
                            numeri-mancanti="false"
                            numeri-assegnati="true"
                        ></search-collocazione>

                        <h5>Fino collocazione:</h5>
                        <search-collocazione
                            title="Collocazione ({{ App\Biblioteca\Models\ViewCollocazione::total() }}) - Lettere (*)"
                            name="toCollocazione"
                            url-lettere="{{ route("api.biblioteca.collocazione") }}"
                            numeri-required="true"
                            numero-nuovo="false"
                            numeri-mancanti="false"
                            numeri-assegnati="true"
                        ></search-collocazione>
                        <button
                            class="btn btn-success my-2"
                            name="action"
                            value="add"
                            type="submit"
                        >
                            Aggiungi
                        </button>
                        <button
                            class="btn btn-danger float-right my-2"
                            name="action"
                            value="remove"
                            type="submit"
                        >
                            Rimuovi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
