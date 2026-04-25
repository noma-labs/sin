@extends("patente.index")

@section("content")
    @include("partials.header", ["title" => "Patenti eliminate", "subtitle" => "(numero patenti eliminate: " . $patentiDeleted->total() . ")"])

    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead>
                <tr class="table-warning">
                    <th>Nome Cognome</th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("numero_patente", "Num. Patente") }}
                    </th>
                    <th>
                        {{ App\Traits\SortableTrait::link_to_sorting_action("data_scadenza_patente", "Data Scadenza") }}
                    </th>
                    <th>Categorie</th>
                    <th>Data Eliminazione</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patentiDeleted as $patente)
                    <tr class="table-primary" hoverable>
                        <td>
                            @if ($patente->persona->nome == null or $patente->persona->cognome == null)
                                {{ $patente->persona->nominativo }}
                            @else
                                {{ $patente->persona->nome }}
                                {{ $patente->persona->cognome }}
                            @endif

                            @if ($patente->hasCommissione())
                                <span class="badge bg-warning">C.</span>
                            @endif

                            @isset($patente->note)
                                <span class="badge bg-success">N.</span>
                            @endisset
                        </td>
                        <td>{{ $patente->numero_patente }}</td>
                        <td>{{ $patente->data_scadenza_patente }}</td>
                        <td>{{ $patente->categorieAsString() }}</td>
                        <td>
                            {{ $patente->deleted_at->format("d/m/Y H:i") }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Nessuna patente eliminata
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-md-6 offset-md-4">
            {{ $patentiDeleted->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
        </div>
    </div>
@endsection
