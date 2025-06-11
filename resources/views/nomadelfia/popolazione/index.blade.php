@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Popolazione", "subtitle" => $population->count()])

    <div class="d-flex flex-wrap gap-2 mb-2">
        <a
            href="{{ route("nomadelfia.popolazione", array_merge(Request::input(), ["age" => "overage"])) }}"
            class="btn btn-sm btn-outline-secondary"
        >
            Maggiorenni
        </a>
        <a
            href="{{ route("nomadelfia.popolazione", array_merge(Request::input(), ["age" => "underage"])) }}"
            class="btn btn-sm btn-outline-secondary"
        >
            Minorenni
        </a>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-2">
        <a
            href="{{ route("nomadelfia.popolazione", ["position" => "effettivo"]) }}"
            class="btn btn-sm btn-outline-secondary"
        >
            Effettivi
        </a>
        <a
            href="{{ route("nomadelfia.popolazione", ["position" => "postulante"]) }}"
            class="btn btn-sm btn-outline-secondary"
        >
            Postulanti
        </a>
        <a
            href="{{ route("nomadelfia.popolazione", ["position" => "ospite"]) }}"
            class="btn btn-sm btn-outline-secondary"
        >
            Ospiti
        </a>

        <a
            href="{{ route("nomadelfia.popolazione", ["position" => "figlio"]) }}"
            class="btn btn-sm btn-outline-secondary"
        >
            Figli
        </a>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-2">
        <a
            href="{{ route("nomadelfia.popolazione", array_merge(Request::input(), ["sex" => "male"])) }}"
            class="btn btn-sm btn-outline-secondary"
        >
            Maschio
        </a>
        <a
            href="{{ route("nomadelfia.popolazione", array_merge(Request::input(), ["sex" => "female"])) }}"
            class="btn btn-sm btn-outline-secondary"
        >
            Femmina
        </a>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-2">
        <a
            href="{{ route("nomadelfia.popolazione") }}"
            class="btn btn-sm btn-outline-secondary"
        >
            Reset
        </a>
    </div>

    <a
        href="{{ route("nomadelfia.popolazione.export.excel", Request::input()) }}"
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
                    {{ App\Traits\SortableTrait::link_to_sorting_action("cf", "Codice Fiscale") }}
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
                <th>Operazioni</th>
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
                    <td>{{ $persona->cf }}</td>
                    <td>{{ $persona->posizione }}</td>
                    <td>{{ $persona->gruppo }}</td>
                    <td>{{ $persona->famiglia }}</td>
                    <td>{{ $persona->azienda }}</td>
                    <td>
                        <a
                            href="{{ route("nomadelfia.person.show", ["id" => $persona->id]) }}"
                            class="btn btn-warning"
                            role="button"
                        >
                            Dettaglio
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
