@extends("biblioteca.libri.index")

@section("content")
    <div id="results" class="alert alert-success">
        Numero di libri Trovati:
        <strong>{{ $libriEliminati->total() }}</strong>
    </div>

    @if ($libriEliminati->total() > 0)
        <!-- table-striped  table-sm -->
        <table id="table" class="table table-bordered">
            <thead class="thead-inverse">
                <tr>
                    <th style="width: 7%" style="font-size: 10px">STATO</th>
                    <th style="width: 7%" style="font-size: 10px">NOTE</th>

                    <th style="width: 10%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("COLLOCAZIONE", "COLLOC") }}
                    </th>
                    <th style="width: 30%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("TITOLO") }}
                    </th>
                    <th style="width: 10%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("AUTORE") }}
                    </th>
                    <!-- <th style="width: 10%"  style="font-size:10px" >Autore (nuovo)</th> -->
                    <th style="width: 12%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("EDITORE") }}
                    </th>
                    <!-- <th style="width: 12%"  style="font-size:10px" >Editore (nuovo)</th> -->
                    <th style="width: 18%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("CLASSIFICAZIONE") }}
                    </th>
                    <th style="width: 20%" style="font-size: 10px">
                        {{ App\Traits\SortableTrait::link_to_sorting_action("NOTE") }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($libriEliminati as $libro)
                    <tr>
                        <td>{{ $libro->deleted_at }}</td>
                        <td>{{ $libro->deleted_note }}</td>
                        <td onclick="gotoLibroDetails({{ $libro->id }})">
                            {{ $libro->collocazione }}
                        </td>
                        <td onclick="gotoLibroDetails({{ $libro->id }})">
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

                        <td onclick="gotoLibroDetails({{ $libro->id }})">
                            {{ $libro->classificazione->descrizione }}
                        </td>
                        <td onclick="gotoLibroDetails({{ $libro->id }})">
                            {{ $libro->note }}
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
