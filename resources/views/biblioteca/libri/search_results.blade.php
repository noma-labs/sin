@extends("biblioteca.libri.index")

@section("content")
    @include("biblioteca.libri.search_partial")

    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        Ricerca effettuata:
        <strong>{{ $msgSearch }}</strong>
        <a href="#" class="close" data-dismiss="alert" aria-label="close">
            &times;
        </a>
    </div>

    <div id="results" class="alert alert-success">
        Numero di libri Trovati:
        <strong>{{ $libri->total() }}</strong>
    </div>

    @if ($libri->total() > 0)
        <table id="table" class="table table-bordered table-hover table-sm">
            <thead class="thead-inverse">
                <tr>
                    <th style="width: 10%" style="font-size: 10px">STATO</th>
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
                        {{ App\Traits\SortableTrait::link_to_sorting_action("CLASSIFICAZIONE") }}
                    </th>
                    <th style="width: 10%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("NOTE") }}
                    </th>
                    <th style="width: 10%" style="font-size: 10px">
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
                                <span class="badge badge-danger">
                                    In prestito
                                </span>
                            @endif

                            @if ($libro->tobe_printed == 1 and ! $libro->trashed())
                                <span class="badge badge-warning">
                                    In stampa
                                </span>
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
        <div class="row">
            <div class="col-md-6 offset-md-4">
                {{ $libri->appends(request()->except("page"))->links("vendor.pagination.bootstrap-4") }}
            </div>
        </div>
    @endif
@endsection

<!-- #results anchor -->
<script>
    window.location.hash = 'results';
</script>
