@extends("archiviodocumenti.index")

@section("archivio")
    <sin-header title="Ricerca Archivio Libri">
        Numero totale di libri:
        {{ App\ArchivioDocumenti\Models\ArchivioDocumento::count() }}
    </sin-header>

    <form method="GET" action="{{ route("archiviodocumenti.libri.ricerca") }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-3">
                <label for="collocazione" class="control-label">
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
                <label for="xTitolo" class="control-label">Titolo</label>
                <input
                    class="form-control"
                    name="titolo"
                    type="text"
                    placeholder="Inserisci il titolo..."
                />
            </div>

            <div class="col-md-3">
                <label for="autore" class="control-label">Autore</label>
                <input
                    class="form-control"
                    name="autore"
                    type="text"
                    placeholder="Inserisci autore..."
                />
            </div>

            <div class="col-md-3">
                <label for="editore" class="control-label">Editore</label>
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
                <div class="form-group">
                    <label id="lab">&nbsp;</label>
                    <button type="submit" class="btn btn-block btn-primary">
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
            <a href="#" class="close" data-dismiss="alert" aria-label="close">
                &times;
            </a>
        </div>
    @endif

    @if (! empty($libri))
        <div id="results" class="alert alert-success">
            Numero di libri trovate:
            <strong>{{ $libri->total() }}</strong>
        </div>

        <table id="table" class="table table-bordered table-hover table-sm">
            <thead class="thead-inverse">
                <tr>
                    <th style="width: 5%" style="font-size: 10px">STATO</th>
                    <th style="width: 10%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("COLLOCAZIONE", "COLLOC") }}
                    </th>
                    <th style="width: 30%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("TITOLO") }}
                    </th>
                    <th style="width: 10%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("AUTORE") }}
                    </th>
                    <th style="width: 12%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("EDITORE") }}
                    </th>
                    <th style="width: 18%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("LOCALITA") }}
                    </th>
                    <th style="width: 20%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("ANNO") }}
                    </th>
                    <th style="width: 20%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("Cop.SCAT.") }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($libri as $libro)
                    <tr>
                        <td>
                            @if ($libro->stato == 1)
                                <span class="badge badge-warning">
                                    In stampa
                                </span>
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
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Vuo davvero eliminare la patente ?</div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
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
