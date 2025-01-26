@extends("biblioteca.books.index")

@section("content")
    <div id="results" class="alert alert-success">
        Numero di libri Trovati:
        <strong>{{ $libriEliminati->total() }}</strong>
    </div>

    @if ($libriEliminati->total() > 0)
        <table id="table" class="table table-bordered">
            <thead>
                <tr class="table-warning">
                    <th>STATO</th>
                    <th>NOTE</th>

                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("COLLOCAZIONE", "COLLOC") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("TITOLO") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("AUTORE") }}
                    </th>
                    <th >
                        {{ App\Traits\SortableTrait::link_to_sorting_action("EDITORE") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("CLASSIFICAZIONE") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("NOTE") }}
                    </th>
                    <th>OPERAZIONI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($libriEliminati as $libro)
                    <tr class="table-primary" hoverable>
                        <td>{{ $libro->deleted_at }}</td>
                        <td>{{ $libro->deleted_note }}</td>
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
                            {{ $libro->classificazione->descrizione }}
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

        {{ $libriEliminati->appends(request()->except("page"))->links() }}
        <!-- {{ $libriEliminati->appends(request()->input())->links() }} -->
    @endif
@endsection

<!-- #results anchor -->
<script>
    window.location.hash = 'results';
</script>
