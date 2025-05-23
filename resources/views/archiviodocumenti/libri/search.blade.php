@extends("archiviodocumenti.index")

@section("content")
    @include("partials.header", ["title" => "Ricerca Archivio Libri"])

    <form method="GET" action="{{ route("archiviodocumenti.libri.ricerca") }}">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <label for="collocazione" class="form-label">
                    Collocazione
                </label>
                <input
                    class="form-control"
                    name="collocazione"
                    type="text"
                    placeholder="Inserisci la collocazione..."
                />
            </div>

            <div class="col-md-3">
                <label for="xTitolo" class="form-label">Titolo</label>
                <input
                    class="form-control"
                    name="titolo"
                    type="text"
                    placeholder="Inserisci il titolo..."
                />
            </div>

            <div class="col-md-3">
                <label for="autore" class="form-label">Autore</label>
                <input
                    class="form-control"
                    name="autore"
                    type="text"
                    placeholder="Inserisci autore..."
                />
            </div>

            <div class="col-md-3">
                <label for="editore" class="form-label">Editore</label>
                <input
                    class="form-control"
                    name="editore"
                    type="text"
                    placeholder="Inserisci editore..."
                />
            </div>
        </div>

        <div class="row">
            <div class="col-md-2"></div>

            <div class="col-md-2"></div>

            <div class="col-md-2"></div>

            <div class="col-md-2"></div>

            <div class="col-md-4"></div>
        </div>
        <div class="row">
            <div class="col-md-5"></div>

            <div class="col-md-5"></div>

            <div class="col-md-2">
                <div class="">
                    <label id="lab">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        Ricerca
                    </button>
                </div>
            </div>
        </div>
        <br />
    </form>

    @if (! empty($msgSearch))
        <div
            class="alert alert-warning alert-dismissible fade show"
            role="alert"
        >
            Ricerca effettuata:
            <strong>{{ $msgSearch }}</strong>
            <a
                href="#"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="close"
            >
                &times;
            </a>
        </div>
    @endif

    @if (! empty($libri))
        <div id="results" class="alert alert-success">
            Numero di libri trovate:
            <strong>{{ $libri->total() }}</strong>
        </div>

        <table id="table" class="table table-hover">
            <thead>
                <tr>
                    <th>STATO</th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("COLLOCAZIONE", "COLLOC") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("TITOLO") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("AUTORE") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("EDITORE") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("LOCALITA") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("ANNO") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("Cop.SCAT.") }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($libri as $libro)
                    <tr>
                        <td>
                            @if ($libro->stato == 1)
                                <span class="badge bg-warning">In stampa</span>
                            @endif
                        </td>
                        <td>{{ $libro->foglio }}</td>
                        <td>{{ $libro->titolo }}</td>
                        <td>{{ $libro->autore }}</td>
                        <td>{{ $libro->editore }}</td>
                        <td>{{ $libro->localita }}</td>
                        <td>{{ $libro->anno }}</td>
                        <td>{{ $libro->copie_scatole }}</td>
                    </tr>
                @empty
                    <div class="alert alert-danger">
                        <strong>Nessun risultato ottenuto</strong>
                    </div>
                @endforelse
            </tbody>
        </table>

        <div class="col-md-2 offset-md-5">
            {{ $libri->links("pagination::bootstrap-4") }}
        </div>
    @endif

    <!-- Modal -->
    <div
        class="modal fade"
        id="eliminaModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalEliminaPatente"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEliminaPatente">
                        Elimina Patente
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">Vuo davvero eliminare la patente ?</div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
