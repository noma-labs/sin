@extends("archiviodocumenti.index")

@section("content")
    @include("partials.header", ["title" => "Stampa Etichette"])

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                Etichette da stampare:
                <strong>
                    {{ App\ArchivioDocumenti\Models\ArchivioDocumento::TobePrinted()->count() }}
                </strong>
                <a
                    href="#"
                    class="btn-close"
                    data-bs-dismiss="alert"
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
                    <!-- <a class="btn btn-info"   href="{{ route("books.labels.preview") }}"  role="button">Anteprima </a> -->
                    <a
                        class="btn btn-success"
                        href="{{ route("libri.etichette.esporta") }}"
                        role="button"
                    >
                        Esporta etichette
                    </a>
                    <form
                        class="float-end"
                        id="formRemoveAll"
                        action="{{ route("archiviodocumenti.etichette.rimuovi") }}"
                        method="post"
                    >
                        {{ method_field("DELETE") }}
                        @csrf
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
                                        <td>
                                            {{ $libro->foglio }}
                                        </td>
                                        <td>
                                            {{ $libro->titolo }}
                                        </td>
                                        <td>
                                            <form
                                                action="{{ route("archiviodocumenti.etichette.rimuovi.singolo", ["id" => $libro->id]) }}"
                                                method="post"
                                            >
                                                {{ method_field("DELETE") }}
                                                @csrf
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
                        action="{{ route("archiviodocumenti.etichette.aggiungi") }}"
                    >
                        @csrf
                        <h5>Dalla collocazione:</h5>
                        <label class="form-label">Collocazione</label>
                        <input type="text" name="fromCollocazione" />

                        <h5>Fino alla collocazione:</h5>
                        <label class="form-label">Collocazione</label>
                        <input type="text" name="toCollocazione" />
                        <button
                            class="btn btn-success my-2"
                            name="action"
                            value="add"
                            type="submit"
                        >
                            Aggiungi
                        </button>
                        <button
                            class="btn btn-danger float-end my-2"
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
