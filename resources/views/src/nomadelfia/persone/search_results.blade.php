@extends("nomadelfia.index")

@section("title", "Ricerca Persone")

@section("content")
    @include("partials.header", ["title" => "Ricerca Persone"])

    @include("nomadelfia.persone.search_form")

    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        Ricerca effettuata:
        <strong>{{ $msgSearch }}</strong>
        <a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close">
            &times;
        </a>
    </div>

    <div id="results" class="alert alert-success">
        Numero di persone trovate:
        <strong>{{ $persone->total() }}</strong>
    </div>

    <!-- inizio tabella persone -->
    <div class="table-responsive">
        <table
            class="table table-hover table-bordered"
            style="table-layout: auto; overflow-x: scroll"
        >
            <thead class="thead-inverse">
                <tr>
                    <th>Numero Elenco</th>
                    <th>Nominativo</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Data Nascita</th>
                    <th>Luogo di Nascita</th>
                    <th>Operazioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($persone as $persona)
                    @empty($persona->delated_at)
                        <tr hoverable>
                            <td>
                                <span class="badge text-bg-primary">
                                    {{ $persona->numero_elenco }}
                                </span>
                            </td>
                            <td>{{ $persona->nominativo }}</td>
                            <td>{{ $persona->nome }}</td>
                            <td>{{ $persona->cognome }}</td>
                            <td>{{ $persona->data_nascita }}</td>
                            <td>{{ $persona->provincia_nascita }}</td>
                            <td>
                                <div
                                    class="button-group btn-block"
                                    role="group"
                                >
                                    <a
                                        class="btn btn-warning btn-sm"
                                        href="{{ route("nomadelfia.persone.dettaglio", ["idPersona" => $persona->id]) }}"
                                    >
                                        Dettaglio
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endempty
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-6 offset-md-4">
            {{ $persone->appends(request()->except("page"))->links("vendor.pagination.bootstrap-4") }}
        </div>
    </div>
@endsection
