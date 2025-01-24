@extends("biblioteca.books.index")

@section("content")
    @include("biblioteca.books.search_partial")

    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        Ricerca effettuata:
        <strong>{{ $msgSearch }}</strong>
        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>
    </div>

    <div id="results" class="alert alert-success">
        Numero di libri Trovati:
        <strong>{{ $libri->total() }}</strong>
    </div>

    @if ($libri->total() > 0)
        <table id="table" class="table table-bordered table-sm">
            <thead class="thead-inverse">
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
                        {{ App\Traits\SortableTrait::link_to_sorting_action("CLASSIFICAZIONE") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("NOTE") }}
                    </th>
                    <th>
                        OPERAZIONI
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($libri as $libro)
                    <tr>
                        <td>
                            @if ($libro->trashed())
                                {{ $libro->deleted_note }}
                            @endif

                            @if ($libro->inPrestito())
                                <span class="badge bg-danger">In prestito</span>
                            @endif

                            @if ($libro->tobe_printed == 1 and ! $libro->trashed())
                                <span class="badge bg-warning">In stampa</span>
                            @endif
                        </td>
                        <td>
                            {{ $libro->collocazione }}
                        </td>
                        <td>
                            {{ $libro->titolo }}
                        </td>

                        @if ($libro->autori->count() > 0)
                            <td>
                                @foreach ($libro->autori as $autore)
                                        {{ $autore->autore }},
                                @endforeach
                            </td>
                        @else
                            <td>{{ $libro->autore }}</td>
                        @endif

                        @if ($libro->editori->count() > 0)
                            <td>
                                @foreach ($libro->editori as $editore)
                                        {{ $editore->editore }},
                                @endforeach
                            </td>
                        @else
                            <td>{{ $libro->editore }}</td>
                        @endif

                        <td>
                            @if ($libro->classificazione)
                                {{ $libro->classificazione->descrizione }}
                            @endif
                        </td>
                        <td>
                            {{ $libro->note }}
                        </td>
                        <td>
                            <a
                                class="btn btn-warning"
                                href="{{ route("books.show", ["id" => $libro->id]) }}"
                                role="button"
                            >
                                Dettaglio
                            </a>
                        </td>
                    </tr>
                @empty
                    <div class="alert alert-danger">
                        <strong>Nessun risultato ottenuto</strong>
                    </div>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $libri->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
        </div>
    @endif
@endsection

<script>
    window.location.hash = 'results';
</script>
