@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Popolazione", "subtitle" => $population->count()])
    <a
        href="{{ route("nomadelfia.popolazione.export.excel") }}"
        class="btn btn-secondary mb-3"
    >
        Esporta Excel
    </a>
    <table class="table table-hover">
        <thead>
            <tr class="table-warning">
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("numero_elenco", "Numero Elenco") }}
                </th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("nome", "Nome") }}
                </th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("cognome", "Cognome") }}
                </th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("data_nascita", "Data Nascita") }}
                </th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("sesso", "Sesso") }}
                </th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("provincia_nascita", "Provincia Nascita") }}
                </th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("posizione", "Posizione") }}
                </th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("gruppo", "Gruppo") }}
                </th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("famiglia", "Famiglia") }}
                </th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("azienda", "Azienda") }}
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($population as $persona)
                <tr class="table-primary">
                    <td>{{ $persona->numero_elenco }}</td>
                    <td>{{ $persona->nome }}</td>
                    <td>{{ $persona->cognome }}</td>
                    <td>{{ $persona->data_nascita }}</td>
                    <td>{{ $persona->sesso }}</td>
                    <td>{{ $persona->provincia_nascita }}</td>
                    <td>{{ $persona->posizione }}</td>
                    <td>{{ $persona->gruppo }}</td>
                    <td>{{ $persona->famiglia }}</td>
                    <td>{{ $persona->azienda }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
