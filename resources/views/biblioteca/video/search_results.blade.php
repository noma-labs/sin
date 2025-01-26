@extends("biblioteca.video.index")

@section("content")
    @include("biblioteca.video.search_partial")
    <div id="results" class="alert alert-success">
        Numero di video Trovati:
        <strong>{{ $videos->total() }}</strong>
    </div>

    @if ($videos->total() > 0)
        <table id="table" class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("CASSETTA") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("DATA_REGISTRAZIONE") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("DESCRIZIONE") }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($videos as $video)
                    <tr>
                        <td>{{ $video->cassetta }}</td>
                        <td>{{ $video->data_registrazione }}</td>
                        <td>{{ $video->descrizione }}</td>
                    </tr>
                @empty
                    <div class="alert alert-danger">
                        <strong>Nessun risultato ottenuto</strong>
                    </div>
                @endforelse
            </tbody>
        </table>

        {{ $videos->appends(request()->except("page"))->links() }}
    @endif
@endsection

<!-- #results anchor -->
<script>
    window.location.hash = 'results';
</script>
